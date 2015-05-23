<?php
session_start();

require_once 'functions.php';
require_once 'helpers/accountshelper.php';
require_once 'helpers/templater.php';
require_once 'helpers/meekrodb.php';
require_once 'templates/pluginitem.php';
require_once 'templates/commentbox.php';
require_once 'templates/immersiveform.php';

$Template = new Templater();
$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (!isset($_GET['id']))
{
	ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'No such entry found', $Template);
	$Template->SetRefresh();
	return;
}

$Query = $SQLLink->queryFirstRow('SELECT * FROM PluginData WHERE UniqueID = %i', $_GET['id']);
if ($Query === null)
{
	ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'No such entry found', $Template);
	$Template->SetRefresh();
	return;
}

if (isset($_POST['Submit']))
{
	if (!AccountsHelper::GetLoggedInUsername($Username))
	{
		ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'You must be logged in to submit a comment', $Template);
	}
	else
	{
		$SQLLink->insert('Comments', array(
			'LinkedPluginUniqueID' => $_GET['id'],
			'Comment' => $_POST['Comment'],
			'AuthorUsername' => $Username
			)
		);

		ImmersiveFormTemplate::AddImmersiveDialog('Operation successful', IMMERSIVE_INFO, 'Your comment was successfully added', $Template);
		$Template->SetRefresh($_SERVER['PHP_SELF']);
	}
	return;
}

$AccountsHelper = new AccountsHelper;
PluginItemTemplate::AddExpandedPluginItem($Query, $Template, $AccountsHelper);

$IsLoggedIn = AccountsHelper::GetLoggedInUsername();
$Query = $SQLLink->query('SELECT * FROM Comments WHERE LinkedPluginUniqueID = %i', $_GET['id']);
if (($SQLLink->count() !== 0) || $IsLoggedIn)
{
	CommentBoxTemplate::BeginCommentsBox($Template);
	if ($IsLoggedIn)
	{
		CommentBoxTemplate::AddCommentsPostingForm($Template, $_GET['id'], $SQLLink->count() !== 0);	
	}
	
	foreach ($Query as $Value)
	{
		CommentBoxTemplate::AddCommentsDisplay($Value['Comment'], $AccountsHelper->GetDetailsFromUsername($Value['AuthorUsername']), $Template);
	}
	CommentBoxTemplate::EndCommentsBox($Template);	
}

$Template->BeginTag('script', array('type' => 'application/javascript', 'src' => 'slideshow.js'));
$Template->EndLastTag();
$Template->BeginTag('script', array());
	$Template->Append('makeBSS(\'.num1\');');
$Template->EndLastTag();

?>