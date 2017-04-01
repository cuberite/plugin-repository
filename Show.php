<?php
session_start();

if (!isset($_GET['RepositoryID']))
{
	http_response_code(400);
	return;
}

require_once '../composer/vendor/autoload.php';
require_once 'Globals.php';
require_once 'Environment Interfaces/Cache.php';
require_once 'Environment Interfaces/Session.php';

$PluginBaseDirectory = Cache::GetCacheDir() . DIRECTORY_SEPARATOR . CacheType::ExpandedPlugins;
$StaticRepositoryFile = $_GET['RepositoryID'] . '.html';

if (!is_file($PluginBaseDirectory . DIRECTORY_SEPARATOR . $StaticRepositoryFile))
{
	http_response_code(404);
	return;
}

Session::GetLoggedInDetails($Details);
$StaticCommentPaths = array();
$CommentsBaseDirectory = Cache::GetCacheDir() . DIRECTORY_SEPARATOR . CacheType::Comments . DIRECTORY_SEPARATOR . $_GET['RepositoryID'];

if (is_dir($CommentsBaseDirectory))
{
	$Result = glob($CommentsBaseDirectory . DIRECTORY_SEPARATOR . '*', GLOB_NOSORT);
	usort(
		$Result,
		function ($A, $B)
		{
			return (int)basename($B, '.html') - (int)basename($A, '.html');
		}
	);
	
	foreach ($Result as $Value)
	{
		$StaticCommentPaths[] = basename($Value);
	}

	$Templater = new Twig_Environment(new Twig_Loader_Filesystem(array('Templates', $PluginBaseDirectory, $CommentsBaseDirectory)), GetTwigOptions());
}
else
{
	$Templater = new Twig_Environment(new Twig_Loader_Filesystem(array('Templates', $PluginBaseDirectory)), GetTwigOptions());
}

$Templater->display(
	'Expanded Plugin.html',
	array(
		'StaticRepositoryPath' => $StaticRepositoryFile,
		'LoginDetails' => $Details,
		'RepositoryID' => $_GET['RepositoryID'],
		'StaticCommentPaths' => $StaticCommentPaths
	)
);
?>