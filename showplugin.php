<?php
session_start();

require_once 'functions.php';
require_once 'helpers/accountshelper.php';
require_once 'helpers/templater.php';
require_once 'templates/pluginitem.php';
require_once 'templates/commentbox.php';
require_once 'templates/immersiveform.php';

$Template = new Templater();
$SQLLink = new mysqli(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if ($SQLLink->connect_errno || !isset($_GET['id']))
{
	ImmersiveFormTemplate::AddImmersiveDialog('No such entry found', IMMERSIVE_ERROR, $Template);
	$Template->SetRedirect();
	return;
}

$ID = $_GET['id'];
$Query = $SQLLink->query("SELECT * FROM PluginData WHERE UniqueID = '$ID'")->fetch_array();
if (!$Query)
{
	ImmersiveFormTemplate::AddImmersiveDialog('No such entry found', IMMERSIVE_ERROR, $Template);
	$Template->SetRedirect();
	return;
}

if (isset($_POST['Submit']))
{
	if (
		!AccountsHelper::GetLoggedInDetails($Details) or
		GetAndVerifyPostData($Comment, 'Comment', $SQLLink)
		)
	{
		DisplayHTMLMessage('Bad input or login status', $_SERVER['PHP_SELF']);
	}
	else
	{
		$SQLLink->query(
			"INSERT INTO Comments (LinkedPluginUniqueID, Comment)
			VALUES ('$ID', '$Comment')"
		);

		ImmersiveFormTemplate::AddImmersiveDialog('Operation successful', IMMERSIVE_INFO, 'Your comment was successfully added', $Template);
		$Template->SetRedirect($_SERVER['PHP_SELF'] . '?id=' . $Query['UniqueID']);
	}
	return;
}

PluginItemTemplate::AddExpandedPluginItem($Query, $Template);

if (AccountsHelper::GetLoggedInDetails($Details))
{
	$Query = $SQLLink->query("SELECT * FROM Comments WHERE LinkedPluginUniqueID = '$ID'");
	CommentBoxTemplate::BeginCommentsBox($Template);
	CommentBoxTemplate::AddCommentsPostingForm($Template, $ID, $Query);
	
	if ($Query)
	{
		for ($Value = $Query->fetch_array(); $Value !== null; $Value = $Query->fetch_array())
		{
			CommentBoxTemplate::AddCommentsDisplay($Value['Comment'], $Template, $Details);
		}
	}
	
	CommentBoxTemplate::EndCommentsBox($Template);
}

$Template->BeginTag('script', array('type' => 'application/javascript', 'src' => 'slideshow.js'));
$Template->EndLastTag();
$Template->BeginTag('script', array());
	$Template->Append('makeBSS(\'.num1\');');
$Template->EndLastTag();

?>