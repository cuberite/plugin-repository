<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
require_once 'helpers/imagehelper.php';
require_once 'helpers/accountshelper.php';
require_once 'templates/pluginitem.php';

$Response = $SQLLink->query('SELECT * FROM PluginData');

if ($SQLLink->count() === 0)
{
	$Template->BeginTag('h2', array('style' => 'text-align: center;'));
		$Template->Append('It\'s lonely in here; did someone remove all of my entries?');
	$Template->EndLastTag();
	return;
}

$Template->BeginTag('div', array('style' => 'text-align: center'));
	$MinimumDuration = 100;
	foreach ($Response as $Value)
	{
		$MinimumDuration = rand($MinimumDuration, 1000);
		PluginItemTemplate::AddCondensedPluginItem($MinimumDuration, $Value, $Template);
	}
$Template->EndLastTag();
?>
