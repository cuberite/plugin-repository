<?php
session_start();

require_once '../composer/vendor/autoload.php';
require_once 'Globals.php';
require_once 'Environment Interfaces/meekrodb.php';
require_once 'Environment Interfaces/Session.php';
require_once 'Environment Interfaces/GitHub API/Repositories.php';
require_once 'Generators/Condensed Plugin.php';
require_once 'Generators/Expanded Plugin.php';

$Templater = new Twig_Environment(new Twig_Loader_Filesystem(array('Templates')), GetTwigOptions());
$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (!Session::GetLoggedInDetails($AuthorDetails))
{
	SetRedirect('//login?' . http_build_query(array('login' => 1, 'redirect' => $_SERVER['REQUEST_URI'])));
	return;
}

if (isset($_POST['Submit']))
{
	$HookID = GitHubAPI\Repositories::CreateUpdateHook($_POST['RepositoryID']);
	
	try 
	{
		$SQLLink->insert(
			'PluginData',
			array(
				'RepositoryID' => $_POST['RepositoryID'],
				'AuthorID' => $AuthorDetails->User['id'],
				'DownloadCount' => 0,
				'UpdateHookID' => $HookID
			)
		);
	}
	catch (MeekroDBException $Exception)
	{
		GitHubAPI\Repositories::DeleteUpdateHook($_POST['RepositoryID'], $HookID);
				
		// TODO: use $Exception->getMessage() and IMMERSIVE_ERROR
		http_response_code(500);
		return;
	}
	
	CondensedPluginModuleGenerator::GenerateAndCache($_POST['RepositoryID']);
	ExpandedPluginModuleGenerator::GenerateAndCache($_POST['RepositoryID']);

	$Templater->display(
		'Immersive Dialog.html',
		array(
			'Message' => 'Operation successful',
			'Explanation' => 'The entry was successfully added.',
			'DialogType' => IMMERSIVE_INFO,
			'LoginDetails' => $AuthorDetails
		)
	);
	SetRefresh();
	return;
}

$Templater->display(
	'Create Plugin Form.html',
	array(
		'RedirectLocation' => '/add',
		'RepositoryGroup' => GitHubAPI\Repositories::GetAllCurrentUserRepositories(),
		'LoginDetails' => $AuthorDetails
	)
);
?>