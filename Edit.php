<?php
session_start();

require_once 'Globals.php';
require_once 'Environment Interfaces/Session.php';
require_once 'Environment Interfaces/GitHub API/Repositories.php';

if (!isset($_GET['RepositoryId']))
{
	http_response_code(400);
	return;
}

$RepositoryId = $_GET['RepositoryId'];

if (isset($_POST['Cancel']))
{
	SetRedirect(WebURI::Show . '/' . $RepositoryId);
	return;
}

$Details = Session::GetLoggedInDetails();
if (!$Details->LoggedIn)
{
	SetRedirect(WebURI::Login . '?' . http_build_query(array('login' => 1, 'redirect' => $_SERVER['REQUEST_URI'])));
	return;
}

$Query = DB::queryFirstRow(
	'SELECT AuthorId, DownloadCount, UpdateHookId, RepositoryName, RepositoryFullName, Description, IconHyperlink
	FROM PluginData WHERE RepositoryId = %i',
	$RepositoryId
);
if ($Query === null)
{
	http_response_code(404);
	return;
}

if ($Details->User->AuthorId !== (int)$Query['AuthorId'])
{
	http_response_code(403);
	return;
}

$Templater = new \Twig\Environment(GetTwigLoader(), GetTwigOptions());

if (isset($_POST['DeleteConfirmed' . $RepositoryId]))
{
	// TODO: catch thrown exceptions
	// TODO (future proofing): race condition if author ever changes between the check and the delete
	GitHubAPI\Repositories::DeleteUpdateHook($RepositoryId, $Query['UpdateHookId']);
	DB::delete('PluginData', 'RepositoryId = %i', $RepositoryId);

	$Templater->display(
		'Immersive Dialog.html',
		array(
			'Message' => 'Operation successful',
			'Explanation' => 'The entry was successfully deleted.',
			'LoginDetails' => $Details
		)
	);

	SetRefresh();
	return;
}

$Templater->display(
	'Immersive Confirmation Dialog.html',
	array(
		'Message' => 'Plugin deletion confirmation',
		'AcceptRedirectLocation' => WebURI::Edit . '/' . $RepositoryId,
		'ConfirmationButtonName' => 'DeleteConfirmed' . $RepositoryId,
		'LoginDetails' => $Details,

		'ContentTemplate' => 'Modules/Delete Plugin Content.html',
		'Explanation' => 'This action will reset all ratings and comments. Are you sure?',
		'Plugin' => $Query
	)
);
?>