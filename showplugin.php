<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$Query = $SQLLink->queryFirstRow('SELECT * FROM PluginData WHERE RepositoryID = %i', $_GET['id']);
if ($Query === null)
{
	ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'No such entry found', $Template);
	$Template->SetRefresh();
	return;
}

if (isset($_POST['Submit']))
{
	if (!AccountsHelper::GetLoggedInDetails($Details))
	{
		ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'You must be logged in to submit a comment', $Template);
	}
	else
	{
		list($AuthorName) = $Details;
		$SQLLink->insert('Comments', array(
				'LinkedRepositoryID' => $_GET['id'],
				'Comment' => $_POST['Comment'],
				'AuthorID' => $AuthorName
			)
		);

		ImmersiveFormTemplate::AddImmersiveDialog('Operation successful', IMMERSIVE_INFO, 'Your comment was successfully added', $Template);
		$Template->SetRefresh($_SERVER['PHP_SELF'] . '?id=' . $_GET['id']);
	}
	return;
}

PluginItemTemplate::AddExpandedPluginItem($Query, $Template);

$IsLoggedIn = AccountsHelper::GetLoggedInDetails();
$Query = $SQLLink->query('SELECT * FROM Comments WHERE LinkedRepositoryID = %i', $_GET['id']);
if (($SQLLink->count() !== 0) || $IsLoggedIn)
{
	CommentBoxTemplate::BeginCommentsBox($Template);
	if ($IsLoggedIn)
	{
		CommentBoxTemplate::AddCommentsPostingForm($Template, $_GET['id'], $SQLLink->count() !== 0);	
	}
	
	foreach ($Query as $Value)
	{
		CommentBoxTemplate::AddCommentsDisplay($Value['Comment'], AccountsHelper::GetDetailsFromID($Value['AuthorID']), $Template);
	}
	CommentBoxTemplate::EndCommentsBox($Template);	
}

$Template->BeginTag('script', array('type' => 'application/javascript', 'src' => 'slideshow.js'));
$Template->EndLastTag();
$Template->BeginTag('script', array());
	$Template->Append('makeBSS(\'.num1\');');
$Template->EndLastTag();

?>