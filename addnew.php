<?php
session_start();

require_once 'functions.php';
require_once 'helpers/templater.php';
require_once 'helpers/meekrodb.php';
require_once 'helpers/githubapihelper.php';
require_once 'templates/immersiveform.php';
require_once 'templates/standardform.php';

$Template = new Templater();
$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (!AccountsHelper::GetLoggedInDetails($AuthorDetails))
{
	$Template->SetRedirect('login.php?' . http_build_query(array('login' => 1, 'redirect' => $_SERVER['REQUEST_URI'])));
	return;
}

if (isset($_POST['Submit']))
{
	$HookID = GitHubAPI::CreateRepositoryUpdateHook($_POST['RepositoryID']);
	
	try 
	{
		$SQLLink->insert(
			'PluginData',
			array(
				'RepositoryID' => $_POST['RepositoryID'],
				'AuthorID' => $AuthorDetails[0],
				'DownloadCount' => 0,
				'UpdateHookID' => $HookID
			)
		);
	}
	catch (MeekroDBException $Exception)
	{
		GitHubAPI::DeleteRepositoryUpdateHook($_POST['RepositoryID'], $HookID);
		ImmersiveFormTemplate::AddImmersiveDialog('The operation failed', IMMERSIVE_ERROR, $Exception->getMessage(), $Template);
		$Template->SetRefresh();
		return;
	}
	
	GitHubAPI::ProcessRepositoryProperties($_POST['RepositoryID']);

	ImmersiveFormTemplate::AddImmersiveDialog('Operation successful', IMMERSIVE_INFO, 'The entry was successfully added', $Template);
	$Template->SetRefresh();
	return;
}

StandardFormTemplate::AddCreatePluginForm($Template);
?>