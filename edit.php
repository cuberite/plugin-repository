<?php
session_start();

require_once 'functions.php';
require_once 'helpers/templater.php';
require_once 'helpers/meekrodb.php';
require_once 'helpers/cachehelper.php';
require_once 'helpers/accountshelper.php';
require_once 'templates/immersiveform.php';
require_once 'templates/standardform.php';

$Template = new Templater();
$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (!isset($_GET['id']))
{
	ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'No plugin ID was specified.', $Template);
	$Template->SetRefresh();
	return;
}

$Query = $SQLLink->queryFirstRow('SELECT * FROM PluginData WHERE RepositoryID = %i', $_GET['id']);
if ($Query === null)
{
	ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'The specified plugin ID does not exist.', $Template);
	$Template->SetRefresh();
	return;
}

if (!AccountsHelper::GetLoggedInDetails($Details) || ($Details[0] != $Query['AuthorID']))
{
	ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'You can only edit your own plugins.', $Template);
	$Template->SetRefresh('showplugin.php?id=' . $_GET['id']);
	return;	
}

if (isset($_POST['DeleteConfirmed' . $_GET['id']]))
{
	GitHubAPI::DeleteRepositoryUpdateHook($_GET['id'], $Query['UpdateHookID']);	
	$SQLLink->query('DELETE FROM PluginData WHERE RepositoryID = %i', $_GET['id']);
	RepositoryResourcesCache::DeleteCache(RepositoryResourcesCache::CACHE_TYPE_REPOSITORYDATA, $_GET['id']);
	ImmersiveFormTemplate::AddImmersiveDialog('Operation successful', IMMERSIVE_INFO, 'The entry was successfully deleted.', $Template);
	$Template->SetRefresh();
	return;
}

if (isset($_POST['Delete' . $_GET['id']]))
{
	ImmersiveFormTemplate::AddImmersiveConfirmationDialog(
		'Plugin deletion confirmation',
		'This action will reset all ratings and comments. Are you sure?',
		'DeleteConfirmed' . $_GET['id'],
		$_SERVER['PHP_SELF'] . '?id=' . $_GET['id'],
		'showplugin.php?id=' . $_GET['id'],
		$Template
	);
	return;
}

StandardFormTemplate::AddEditPluginForm($Query, $Template);
?>