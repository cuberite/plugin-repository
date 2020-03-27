<?php
session_start();

require_once 'Globals.php';
require_once 'Models/Author.php';
require_once 'Models/Plugin.php';
require_once 'Environment Interfaces/Session.php';
require_once 'Environment Interfaces/GitHub API/Repositories.php';

$Templater = new \Twig\Environment(GetTwigLoader(), GetTwigOptions());

$AuthorDetails = Session::GetLoggedInDetails();
if (!$AuthorDetails->LoggedIn)
{
	SetRedirect(WebURI::Login . '?' . http_build_query(array('login' => 1, 'redirect' => $_SERVER['REQUEST_URI'])));
	return;
}

if (isset($_POST['Submit']))
{
	$RepositoryId = $_POST['RepositoryId'];

	// Modify database first before creating webhook to avoid race conditions
	// E.g. hook notification before insert happens, thus missing an update

	DB::insertUpdate(
		'Authors',
		array(
			'AuthorId' => $AuthorDetails->User->AuthorId,
			'Login' => $AuthorDetails->User->Login,
			'DisplayName' => $AuthorDetails->User->DisplayName,
			'AvatarHyperlink' => $AuthorDetails->User->AvatarHyperlink
		)
	);
	PluginGenerator::GenerateAndStore($RepositoryId, $AuthorDetails->User->AuthorId);
	$HookId = GitHubAPI\Repositories::CreateUpdateHook($RepositoryId);
	PluginGenerator::UpdateWebhook($RepositoryId, $HookId); // TODO: check actually updated

	// TODO: use $Exception->getMessage() and IMMERSIVE_ERROR for error handling

	$Templater->display(
		'Immersive Dialog.html',
		array(
			'Message' => 'Operation successful',
			'Explanation' => 'The entry was successfully added.',
			'LoginDetails' => $AuthorDetails
		)
	);

	SetRefresh(WebURI::Show . '/' . $RepositoryId);
	return;
}

$Templater->display(
	'Create Plugin Form.html',
	array(
		'ActiveItems' => array('active'),
		'RepositoryGroup' => GitHubAPI\Repositories::GetAllCurrentUserRepositories(),
		'LoginDetails' => $AuthorDetails
	)
);
?>