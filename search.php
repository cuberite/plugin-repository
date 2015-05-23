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
		case 'UniqueID':
		case 'AuthorName': break;
		default: return;
	}
	
	$Response = $SQLLink->query('SELECT * FROM PluginData WHERE %l LIKE %s', $_POST['Method'], '%' . $_POST['Query'] . '%'	);
	if ($SQLLink->count() === 0)
	{
		$Template->BeginTag('h1', array('style' => 'text-align: center;'));
			$Template->Append('No matching results were found');
		$Template->EndLastTag();
		return;
	}

	$Template->BeginTag('div', array('style' => 'text-align: center'));
		$MinimumDuration = 100;
		foreach ($Response as $Value)
		{
			$MinimumDuration = rand($MinimumDuration, 1000);
			ImageHelper::GetDominantColorAndTextColour($Value['Icon'], $DominantRGB, $TextColour);
			PluginItemTemplate::AddCondensedPluginItem($DominantRGB, $TextColour, $MinimumDuration, $Value, $Template);
		}
	$Template->EndLastTag();
	return;
}

$Template->SetRedirect();
?>