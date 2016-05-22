<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
require_once 'helpers/templater.php';
require_once 'helpers/meekrodb.php';
require_once 'templates/immersiveform.php';
require_once 'templates/standardform.php';

$Template = new Templater();
$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (!AccountsHelper::GetLoggedInDetails($AuthorDetails))
{
	$Template->SetRedirect('login.php?login=1');
	return;
}

if (isset($_POST['Submit']))
{
	try 
	{
		$SQLLink->insert('PluginData', array(
				'RepositoryID' => $_POST['RepositoryID'],
				'AuthorID' => $AuthorDetails[0]
			)
		);
	}
	catch (MeekroDBException $Exception)
	{
		ImmersiveFormTemplate::AddImmersiveDialog('The operation failed', IMMERSIVE_ERROR, $Exception->getMessage(), $Template);
		$Template->SetRefresh();
		return;
	}

	ImmersiveFormTemplate::AddImmersiveDialog('Operation successful', IMMERSIVE_INFO, 'The entry was successfully added', $Template);
	$Template->SetRefresh();
	return;
}

StandardFormTemplate::AddCreatePluginForm($Template);
?>