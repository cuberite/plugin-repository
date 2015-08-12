<?php
session_start();

require_once 'functions.php';
require_once 'helpers/templater.php';
require_once 'helpers/meekrodb.php';
require_once 'helpers/accountshelper.php';
require_once 'templates/immersiveform.php';
require_once 'templates/standardform.php';

$Template = new Templater();
$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (!isset($_GET['id']))
{
	ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'No plugin ID was specified', $Template);
	$Template->SetRefresh();
	return;
}

$Query = $SQLLink->queryFirstRow('SELECT * FROM PluginData WHERE UniqueID = %i', $_GET['id']);
if ($Query === null)
{
	ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'The specified plugin ID does not exist', $Template);
	$Template->SetRefresh();
	return;
}

if (!AccountsHelper::GetLoggedInUsername($Username) || ($Username !== $Query['Author']))
{
	ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'You can only edit your own plugins', $Template);
	$Template->SetRefresh('showplugin.php?id=' . $_GET['id']);
	return;	
}

if (isset($_POST['Delete' . $_GET['id']]))
{
	$SQLLink->query('DELETE FROM PluginData WHERE UniqueID = %i', $_GET['id']);
	RecursivelyDeleteDirectory('uploads' . DIRECTORY_SEPARATOR . $_GET['id']);
	ImmersiveFormTemplate::AddImmersiveDialog('Operation successful', IMMERSIVE_INFO, 'The entry was successfully deleted', $Template);
	$Template->SetRefresh();
	return;
}
if (isset($_POST['Edit' . $_GET['id']]))
{
	$IconString = StoreFile($_FILES['icon']['tmp_name'], $_FILES['icon']['name'], $_GET['id'], $Query['Icon']);
	$PluginString = StoreFile($_FILES['pluginfile']['tmp_name'], $_FILES['pluginfile']['name'], $_GET['id'], $Query['PluginFile']);
	$ImagesString = StoreAndSerialiseImages($_FILES['images']['tmp_name'], $_FILES['images']['name'], $_GET['id'], $Query['Images']);
	echo $ImagesString;

	$SQLLink->update('PluginData', array(
			'PluginName' => $_POST['PluginName'],
			'PluginDescription' => $_POST['PluginDescription'],
			'PluginVersion' => $_POST['PluginVersion'],
			'Icon' => $SQLLink->sqleval('IF(%s = \'\', Icon, %s)', $IconString, $IconString),
			'Images' => $SQLLink->sqleval('IF(%s = \'\', Images, %s)', $ImagesString, $ImagesString),
			'PluginFile' => $SQLLink->sqleval('IF(%s = \'\', PluginFile, %s)', $PluginString, $PluginString)
		),
		'UniqueID = %l', $_GET['id']
	);
	
	ImmersiveFormTemplate::AddImmersiveDialog('Operation successful', IMMERSIVE_INFO, 'The entry was successfully updated', $Template);
	$Template->SetRefresh('showplugin.php?id=' . $_GET['id']);
	return;
}

StandardFormTemplate::AddEditPluginForm($Query, $Template);

$Template->BeginTag('script', array('src' => '//cdn.ckeditor.com/4.4.7/standard/ckeditor.js'));
$Template->EndLastTag();
$Template->BeginTag('script');
	$Template->Append("CKEDITOR.plugins.addExternal('iframe', '/ckeditor/iframe/', 'plugin.js');");
	$Template->Append("CKEDITOR.replace('ckeditor', { skin : 'office2013,/ckeditor/office2013/', extraPlugins : 'iframe' } );");
$Template->EndLastTag();

?>