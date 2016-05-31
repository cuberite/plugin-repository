<?php
session_start();

require_once 'functions.php';
require_once 'helpers/templater.php';
require_once 'helpers/imagehelper.php';
require_once 'helpers/meekrodb.php';
require_once 'templates/pluginitem.php';

$Template = new Templater();
$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (isset($_POST['Search']) && isset($_POST['Method']))
{
	switch ($_POST['Method'])
	{
		case 'PluginName':
		{
			$Response = array();
			$AllPlugins = $SQLLink->query('SELECT * FROM PluginData');
			foreach ($AllPlugins as $Plugin)
			{
				if (GitHubAPI::CustomRequest('repositories', $Plugin['RepositoryID'])['name'] == $_POST['Query'])
				{
					$Response[] = $Plugin;
				}
			}
			break;
		}
		case 'RepositoryID':
		case 'AuthorID':
		{			
			$Response = $SQLLink->query('SELECT * FROM PluginData WHERE %l LIKE %s', $_POST['Method'], '%' . $_POST['Query'] . '%'	);
			break;
		}
		default: return;
	}
	
	if (empty($Response))
	{
		$Template->BeginTag('h1', array('style' => 'text-align: center;'));
			$Template->Append('No matching results were found');
		$Template->EndLastTag();
		return;
	}

	$Template->BeginTag('section', array('class' => 'boundedbox'));
		$MinimumDuration = 100;
		foreach ($Response as $Value)
		{
			$MinimumDuration = rand($MinimumDuration, 1000);
			PluginItemTemplate::AddCondensedPluginItem($MinimumDuration, $Value, $Template);
		}
	$Template->EndLastTag();
	return;
}

$Template->SetRedirect();
?>