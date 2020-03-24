<?php
session_start();

require_once '../composer/vendor/autoload.php';
require_once 'Globals.php';
require_once 'Environment Interfaces/meekrodb.php';
require_once 'Environment Interfaces/Cache.php';
require_once 'Environment Interfaces/Session.php';
require_once 'Environment Interfaces/imagehelper.php';
require_once 'Environment Interfaces/GitHub API/Repositories.php';
require_once 'Environment Interfaces/GitHub API/Users.php';

$BaseDirectory = Cache::GetCacheDir() . DIRECTORY_SEPARATOR . CacheType::CondensedPlugins;
$Templater = new \Twig\Environment(new \Twig\Loader\FilesystemLoader(array('Templates', $BaseDirectory)), GetTwigOptions());
$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (isset($_POST['Search']) && isset($_POST['Method']))
{
	switch ($_POST['Method'])
	{
		case 'AuthorName':
		{
			$AllPlugins = $SQLLink->query('SELECT * FROM PluginData');
			foreach ($AllPlugins as $Plugin)
			{
				$Author = GitHubAPI\Users::GetDetailsFromID($Plugin['AuthorID']);
				if (
					(stripos($Author['login'], $_POST['Query']) !== false) ||
					(stripos($Author['name'], $_POST['Query']) !== false)
				)
				{
					$Response[] = $Plugin;
				}
			}
			break;
		}
		case 'PluginName':
		{
			$AllPlugins = $SQLLink->query('SELECT * FROM PluginData');
			foreach ($AllPlugins as $Plugin)
			{
				list($RepositoryName) = GitHubAPI\Repositories::GetMetadata($Plugin['RepositoryID']);
				if (stripos($RepositoryName, $_POST['Query']) !== false)
				{
					$Response[] = $Plugin;
				}
			}
			break;
		}
		case 'RepositoryID':
		{
			$Response = $SQLLink->query('SELECT * FROM PluginData WHERE RepositoryID = %s', $_POST['Query']);
			break;
		}
		default:
		{
			http_response_code(400);
			return;
		}
	}

	Session::GetLoggedInDetails($Details);
	$Templater->display(
		'Condensed Plugins.html',
		array(
			'StaticRepositoryPaths' => array_map(
				function($Value)
				{
					return $Value['RepositoryID'] . '.html';
				},
				$Response
			),
			'LoginDetails' => $Details
		)
	);
	return;
}

http_response_code(400);
?>