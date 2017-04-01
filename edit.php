<?php
session_start();

require_once '../composer/vendor/autoload.php';
require_once 'Globals.php';
require_once 'Environment Interfaces/meekrodb.php';
require_once 'Environment Interfaces/Session.php';
require_once 'Environment Interfaces/GitHub API/Repositories.php';

if (!isset($_GET['RepositoryID']))
{
	http_response_code(400);
	return;
}

if (isset($_POST['Cancel']))
{
	SetRedirect('//show/' . $_GET['RepositoryID']);
	return;
}

$Templater = new Twig_Environment(new Twig_Loader_Filesystem(array('Templates')), GetTwigOptions());
$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (!Session::GetLoggedInDetails($Details))
{
	SetRedirect('//login?' . http_build_query(array('login' => 1, 'redirect' => $_SERVER['REQUEST_URI'])));
	return;
}

$Query = $SQLLink->queryFirstRow('SELECT * FROM PluginData WHERE RepositoryID = %i', $_GET['RepositoryID']);
if ($Query === null)
{
	http_response_code(404);
	return;
}

if ($Details->User['id'] !== (int)$Query['AuthorID'])
{
	http_response_code(403);
	return;
}

if (isset($_POST['DeleteConfirmed' . $_GET['RepositoryID']]))
{
	// TODO: catch thrown exceptions
	// TODO (future proofing): race condition if author ever changes between the check and the delete
	$SQLLink->query('DELETE FROM PluginData WHERE RepositoryID = %i', $_GET['RepositoryID']);

	GitHubAPI\Repositories::DeleteUpdateHook($_GET['RepositoryID'], $Query['UpdateHookID']);
	Cache::DeleteCache(CacheType::CondensedPlugins, $_GET['RepositoryID'] . '.html');
	Cache::DeleteCache(CacheType::ExpandedPlugins, $_GET['RepositoryID'] . '.html');

	$Templater->display(
		'Immersive Dialog.html',
		array(
			'Message' => 'Operation successful',
			'Explanation' => 'The entry was successfully deleted.',
			'DialogType' => IMMERSIVE_INFO,
			'LoginDetails' => $Details
		)
	);
	SetRefresh();
	return;
}

if (isset($_POST['Delete' . $_GET['RepositoryID']]))
{
	$Templater->display(
		'Immersive Confirmation Dialog.html',
		array(
			'Message' => 'Plugin deletion confirmation',
			'Explanation' => 'This action will reset all ratings and comments. Are you sure?',
			'AcceptRedirectLocation' => '/edit/' . $_GET['RepositoryID'],
			'ConfirmationButtonName' => 'DeleteConfirmed' . $_GET['RepositoryID'],
			'LoginDetails' => $Details
		)
	);
	return;
}

$Templater->display('Edit Plugin Form.html', array('RepositoryID' => $_GET['RepositoryID'], 'LoginDetails' => $Details));
?>