<?php
if (!($INIParseResult = parse_ini_file('configuration.ini')))
{
	header('Location: setup.php');
	return;
}

$_SERVER['SERVER_NAME'] = 'plugins.cuberite.org';

define('DB_ADDRESS', $INIParseResult['DatabaseAddress']);
define('DB_USERNAME', $INIParseResult['DatabaseUsername']);
define('DB_PASSWORD', $INIParseResult['DatabasePassword']);
define('DB_PLUGINSDATABASENAME', $INIParseResult['PluginDatabaseName']);
define('GH_OAUTH_CLIENT_ID', $INIParseResult['GitHubClientID']);
define('GH_OAUTH_CLIENT_SECRET', $INIParseResult['GitHubClientSecret']);

const IMMERSIVE_INFO = 0;
const IMMERSIVE_ERROR = 1;

function GetTwigOptions()
{
	require_once 'Environment Interfaces/Cache.php';
	return array('cache' => Cache::GetCacheDir() . DIRECTORY_SEPARATOR . CacheType::Preprocessed, 'auto_reload' => true);
}

function SetRedirect($RedirectAddress = '/')
{
	header("Location: $RedirectAddress");
}

function SetRefresh($RedirectAddress = '/', $Timeout = 1)
{
	header("Refresh: $Timeout; URL=$RedirectAddress");
}