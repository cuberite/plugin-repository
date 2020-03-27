<?php
if (!($INIParseResult = @parse_ini_file('configuration.ini')))
{
	header('Location: Setup.php');
	die;
}

$_SERVER['SERVER_NAME'] = 'plugins.cuberite.org';
define('UPDATE_HOOK_ADDRESS', 'https://plugins.cuberite.org/process-hook');

// autoloader dir ... autoloadir hon hon hon
define('COMPOSER_AUTOLOADIR', join(DIRECTORY_SEPARATOR, array('..\Composer', 'vendor', 'autoload.php')));
define('CACHE_DIR', '..\Template Cache');

define('GH_OAUTH_CLIENT_ID', $INIParseResult['GitHubClientId']);
define('GH_OAUTH_CLIENT_SECRET', $INIParseResult['GitHubClientSecret']);

require_once COMPOSER_AUTOLOADIR;
require_once 'Environment Interfaces/meekrodb.php';

DB::$user = $INIParseResult['DatabaseUsername'];
DB::$password = $INIParseResult['DatabasePassword'];
DB::$dbName = $INIParseResult['PluginDatabaseName'];
DB::$host = $INIParseResult['DatabaseHostname'];
DB::$port = $INIParseResult['DatabasePort'];

final class WebURI
{
	const Home = '/';
	const Add = '/add';
	const Comment = '/comment';
	const Copyright = '/copyright';
	const Download = '/download';
	const Edit = '/edit';
	const Login = '/login';
	const GitHubLogin = 'https://github.com/login/oauth/authorize';
	const GitHubExchangeToken = 'https://github.com/login/oauth/access_token';
	const Logout = '/login?logout=1';
	const Search = '/search';
	const Show = '/show';
}

function GetTwigLoader()
{
	return new \Twig\Loader\FilesystemLoader(array($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .'Templates'));
}

function GetTwigOptions()
{
	return array('cache' => CACHE_DIR, 'auto_reload' => true);
}

function SetRedirect($RedirectAddress = '/')
{
	header("Location: $RedirectAddress");
}

function SetRefresh($RedirectAddress = '/', $Timeout = 1)
{
	header("Refresh: $Timeout; URL=$RedirectAddress");
}