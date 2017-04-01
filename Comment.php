<?php
session_start();

require_once '../composer/vendor/autoload.php';
require_once 'Globals.php';
require_once 'Environment Interfaces/meekrodb.php';
require_once 'Environment Interfaces/Session.php';
require_once 'Environment Interfaces/GitHub API/Repositories.php';
require_once 'Generators/Comment.php';
require_once 'Generators/Expanded Plugin.php';

$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (!isset($_GET['RepositoryID']))
{
	http_response_code(400);
	return;
}

if (isset($_POST['Submit']))
{
	if (!Session::GetLoggedInDetails($Details))
	{
		SetRedirect('//login?' . http_build_query(array('login' => 1, 'redirect' => $_SERVER['REQUEST_URI'])));
		return;
	}
	
	$SQLLink->insert(
		'Comments',
		array(
			'LinkedRepositoryID' => $_GET['RepositoryID'],
			'Comment' => $_POST['Comment'],
			'AuthorID' => $Details->User['id']
		)
	);
	// TODO: catch exceptions

	CommentModuleGenerator::GenerateAndCache($_GET['RepositoryID']);
	ExpandedPluginModuleGenerator::GenerateAndCache($_GET['RepositoryID']);

	// TODO: success message
	//ImmersiveFormTemplate::AddImmersiveDialog('Operation successful', IMMERSIVE_INFO, 'Your comment was successfully added', $Template);
	
	SetRedirect('//show/' . $_GET['RepositoryID']);
	return;
}

http_response_code(400);
?>