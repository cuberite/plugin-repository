<?php
session_start();

require_once 'Globals.php';
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
	$Id = $_POST['RepositoryId'];
	$HookId = GitHubAPI\Repositories::CreateUpdateHook($Id);

	try
	{
		PluginGenerator::GenerateAndStore($Id, $AuthorDetails->User->AuthorId, $HookId);
	}
	catch (MeekroDBException $Exception)
	{
		GitHubAPI\Repositories::DeleteUpdateHook($Id, $HookId);

		// TODO: use $Exception->getMessage() and IMMERSIVE_ERROR
		http_response_code(500);
		return;
	}

	$Templater->display(
		'Immersive Dialog.html',
		array(
			'Message' => 'Operation successful',
			'Explanation' => 'The entry was successfully added.',
			'DialogType' => 1,
			'LoginDetails' => $AuthorDetails
		)
	);

	SetRefresh();
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