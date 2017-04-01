<?php
session_start();

require_once 'functions.php';
require_once 'helpers/templater.php';
require_once 'helpers/imagehelper.php';
require_once 'helpers/accountshelper.php';
require_once 'helpers/meekrodb.php';
require_once 'templates/pluginitem.php';

$Template = new Templater();
$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);
$Response = $SQLLink->query('SELECT * FROM PluginData');

if ($SQLLink->count() === 0)
{
	$Template->BeginTag('h2', array('style' => 'text-align: center;'));
		$Template->Append('It\'s lonely in here; did someone remove all of my entries?');
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
?>
