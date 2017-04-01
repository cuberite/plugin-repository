<?php
session_start();

require_once '../composer/vendor/autoload.php';
require_once 'Globals.php';
require_once 'Environment Interfaces/meekrodb.php';
require_once 'Environment Interfaces/Session.php';
require_once 'Environment Interfaces/GitHub API/Repositories.php';
require_once 'Generators/Expanded Plugin.php';

$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (!isset($_GET['RepositoryID']))
{
	http_response_code(400);
	return;
}

if (isset($_POST['Download']) && isset($_POST['DownloadType']))
{
	if ($SQLLink->update('PluginData', array('DownloadCount' => $SQLLink->sqleval('DownloadCount + 1')), 'RepositoryID = %i', $_GET['RepositoryID']) === false)
	{
		http_response_code(404);
		return;
	}
	
	ExpandedPluginModuleGenerator::GenerateAndCache($_GET['RepositoryID']);
	SetRedirect($_POST['DownloadType']);
	return;
}

$Query = $SQLLink->queryFirstRow('SELECT * FROM PluginData WHERE RepositoryID = %i', $_GET['RepositoryID']);
if ($Query === null)
{
	http_response_code(404);
	return;
}

SetRedirect('https://api.github.com/repos/' . GitHubAPI\Repositories::GetMetadata($_GET['RepositoryID'])[1] . '/zipball');
?>