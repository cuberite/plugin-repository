<?php
session_start();

require_once 'functions.php';
require_once 'helpers/templater.php';
require_once 'templates/immersiveform.php';
require_once 'templates/standardform.php';

$Template = new Templater();
$SQLLink = new mysqli(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (!isset($_GET['id']))
{
	ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'No plugin ID was specified', $Template);
	$Template->SetRefresh();
	return;
}

$ID = $_GET['id'];
$Query = $SQLLink->query("SELECT * FROM PluginData WHERE UniqueID = '$ID'")->fetch_array();
if (!$Query)
{
	ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'The specified plugin ID does not exist', $Template);
	$Template->SetRefresh();
	return;
}

if (isset($_POST["Delete$ID"]))
{
	$SQLLink->query("DELETE FROM PluginData WHERE UniqueID = '$ID'");
	RecursivelyDeleteDirectory('uploads' . DIRECTORY_SEPARATOR . $ID);
	ImmersiveFormTemplate::AddImmersiveDialog('Operation successful', IMMERSIVE_INFO, 'The entry was successfully deleted', $Template);
	$Template->SetRefresh();
	return;
}
if (isset($_POST["Edit$ID"]))
{
	if (
		GetAndVerifyPostData($PluginName, 'PluginName', $SQLLink) or
		GetAndVerifyPostData($PluginDescription, 'PluginDescription', $SQLLink) or
		GetAndVerifyPostData($PluginVersion, 'PluginVersion', $SQLLink)
		)
	{
		ImmersiveFormTemplate::AddImmersiveDialog('An error occurred',  IMMERSIVE_ERROR, 'The input was invalid or malformed', $Template);
		$Template->SetRefresh($_SERVER['PHP_SELF'] . '?id=' . $ID);
		return;
	}
	else
	{
		$IconString = StoreFile($_FILES['icon']['tmp_name'], $_FILES['icon']['name'], $ID);
		$PluginString = StoreFile($_FILES['pluginfile']['tmp_name'], $_FILES['pluginfile']['name'], $SQLLink->insert_id);
		$ImagesString = StoreAndSerialiseImages($_FILES['images']['tmp_name'], $_FILES['images']['name'], $ID);
		$IconString = empty($IconString) ? StoreWebFile('http://www.gravatar.com/avatar/' . hash('md5', strtolower(trim($PluginName))) . '?d=retro&s=200', 'default_icon.png', $ID) : $IconString;

		$SQLLink->query(
			"UPDATE PluginData
				SET
					PluginName = '$PluginName',
					PluginDescription = '$PluginDescription',
					PluginVersion = '$PluginVersion',
					Icon = IF('$IconString' = '', Icon, '$IconString'),
					Images = IF('$ImagesString' = '', Images, '$ImagesString'),
					PluginFile = IF('$PluginString' = '', PluginFile, '$PluginString')
			WHERE UniqueID = '$ID'"
		);
	}
	ImmersiveFormTemplate::AddImmersiveDialog('Operation successful', IMMERSIVE_INFO, 'The entry was successfully updated', $Template);
	$Template->SetRefresh('showplugin.php?id=' . $ID);
	return;
}

$Template->BeginTag('script', array('src' => 'ckeditor/ckeditor.js', 'type' => 'application/javascript'));
$Template->EndLastTag();
StandardFormTemplate::AddEditPluginForm($Query, $Template);

?>