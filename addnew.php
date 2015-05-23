<?php
session_start();

require_once 'functions.php';
require_once 'helpers/templater.php';
require_once 'helpers/meekrodb.php';
require_once 'templates/immersiveform.php';
require_once 'templates/standardform.php';

$Template = new Templater();
$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (!AccountsHelper::GetLoggedInUsername($Author))
{
	$Template->SetRedirect('login.php?login=1');
	return;
}

if (isset($_POST['Submit']))
{
	$SQLLink->insert('PluginData', array(
			'Author' => $Author,
			'PluginName' => $_POST['PluginName'],
			'PluginDescription' => $_POST['PluginDescription'],
			'PluginVersion' => $_POST['PluginVersion']
		)
	);
	
	$IconString = StoreFile($_FILES['icon']['tmp_name'], $_FILES['icon']['name'], $SQLLink->insert_id);
	$PluginString = StoreFile($_FILES['pluginfile']['tmp_name'], $_FILES['pluginfile']['name'], $SQLLink->insert_id);
	$ImagesString = StoreAndSerialiseImages($_FILES['images']['tmp_name'], $_FILES['images']['name'], $SQLLink->insert_id);
	$IconString = empty($IconString) ? StoreWebFile('http://www.gravatar.com/avatar/' . hash('md5', strtolower(trim($_POST['PluginName']))) . '?d=retro&s=200', 'default_icon.png', $SQLLink->insert_id) : $IconString;
	
	$SQLLink->update('PluginData', array(
			'Icon' => $IconString,
			'Images' => $ImagesString,
			'PluginFile' => $PluginString
		),
		'UniqueID = %l', $SQLLink->insert_id
	);

	ImmersiveFormTemplate::AddImmersiveDialog('Operation successful', IMMERSIVE_INFO, 'The entry was successfully added', $Template);
	$Template->SetRefresh();
	return;
}

StandardFormTemplate::AddCreatePluginForm($Template);

$Template->BeginTag('script', array('src' => '//cdn.ckeditor.com/4.4.7/standard/ckeditor.js'));
$Template->EndLastTag();
$Template->BeginTag('script');
	$Template->Append("CKEDITOR.plugins.addExternal('iframe', '/ckeditor/iframe/', 'plugin.js');");
	$Template->Append("CKEDITOR.replace('ckeditor', { skin : 'office2013,/ckeditor/office2013/', extraPlugins : 'iframe' } );");
$Template->EndLastTag();

?>