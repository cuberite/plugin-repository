<?php
session_start();

require_once '../composer/vendor/autoload.php';
require_once 'Globals.php';
require_once 'Environment Interfaces/Cache.php';
require_once 'Environment Interfaces/Session.php';

$BaseDirectory = Cache::GetCacheDir() . DIRECTORY_SEPARATOR . CacheType::CondensedPlugins;
$Templater = new \Twig\Environment(new \Twig\Loader\FilesystemLoader(array('Templates', $BaseDirectory)), GetTwigOptions());

$StaticRepositoryPaths = array();
foreach (glob($BaseDirectory . DIRECTORY_SEPARATOR . '*', GLOB_NOSORT) as $Value)
{
	$StaticRepositoryPaths[] = basename($Value);
}

Session::GetLoggedInDetails($Details);
$Templater->display('Condensed Plugins.html', array('StaticRepositoryPaths' => $StaticRepositoryPaths, 'LoginDetails' => $Details));
?>