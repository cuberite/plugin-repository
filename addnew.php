<?php
session_start();

require_once 'functions.php';
require_once 'helpers/templater.php';
require_once 'templates/immersiveform.php';
require_once 'templates/standardform.php';

$Template = new Templater();
$SQLLink = new mysqli(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (!AccountsHelper::GetLoggedInUsername($Author, $DisplayName))
{
	$Template->SetRedirect('login.php?login=1', 0);
	return;
}

if (isset($_POST['Submit']))
{
	if (
		GetAndVerifyPostData($PluginName, 'PluginName', $SQLLink) or
		GetAndVerifyPostData($PluginDescription, 'PluginDescription', $SQLLink) or
		GetAndVerifyPostData($PluginVersion, 'PluginVersion', $SQLLink)
		)
	{
		ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'The input was invalid or malformed.', $Templater);
		$Template->SetRedirect();
	}
	else
	{
		$SQLLink->query(
			"INSERT INTO PluginData (Author, AuthorDisplayName, PluginName, PluginDescription, PluginVersion)
			VALUES ('$Author', '$DisplayName', '$PluginName', '$PluginDescription', '$PluginVersion')"
		);

		$IconString = StoreFile($_FILES['icon']['tmp_name'], $_FILES['icon']['name'], $SQLLink->insert_id);
		$PluginString = StoreFile($_FILES['pluginfile']['tmp_name'], $_FILES['pluginfile']['name'], $SQLLink->insert_id);
		$ImagesString = StoreAndSerialiseImages($_FILES['images']['tmp_name'], $_FILES['images']['name'], $SQLLink->insert_id);

		$SQLLink->query(
			"UPDATE PluginData
				SET Icon = '$IconString', Images = '$ImagesString', PluginFile = '$PluginString'
			WHERE UniqueID = '$SQLLink->insert_id'"
		);

		ImmersiveFormTemplate::AddImmersiveDialog('Operation successful', IMMERSIVE_INFO, 'The entry was successfully added', $Template);
		$Template->SetRedirect();
	}
	return;
}

$Template->BeginTag('script', array('src' => 'ckeditor/ckeditor.js', 'type' => 'application/javascript'));
$Template->EndLastTag();
StandardFormTemplate::AddCreatePluginForm($Template);

?>