<?php
session_start();

require_once 'functions.php';
require_once 'helpers/templater.php';
require_once 'helpers/imagehelper.php';
require_once 'templates/pluginitem.php';

$Template = new Templater();
$SQLLink = new mysqli(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (isset($_POST['Search']) && isset($_POST['Method']))
{
	if (GetAndVerifyPostData($Query, 'Query', $SQLLink) || GetAndVerifyPostData($Method, 'Method', $SQLLink))
	{
		ImmersiveFormTemplate::AddImmersiveDialog('An error occurred',  IMMERSIVE_ERROR, 'One or more inputs were invalid or malformed', $Template);
		$Template->SetRedirect();
		return;
	}
	else
	{
		$Response = $SQLLink->query("SELECT * FROM PluginData WHERE '$Method' LIKE '%$Query%'");
		echo "SELECT * FROM PluginData WHERE '$Method' LIKE '%$Query%'";
		if ($SQLLink->connect_errno || ($Response->num_rows == 0))
		{
			$Template->BeginTag('h1', array('style' => 'text-align: center;'));
				$Template->Append('No matching results were found');
			$Template->EndLastTag();
			return;
		}

		$Template->BeginTag('div', array('style' => 'text-align: center'));
			$MinimumDuration = 100;
			for ($Value = $Response->fetch_array(); $Value !== null; $Value = $Response->fetch_array())
			{
				$MinimumDuration = rand($MinimumDuration, 1000);
				ImageHelper::GetDominantColorAndTextColour($Value['Icon'], $DominantRGB, $TextColour);
				PluginItemTemplate::AddCondensedPluginItem($DominantRGB, $TextColour, $MinimumDuration, $Value, $Template);
			}
		$Template->EndLastTag();
		return;
	}
}

$Template->SetRedirect();
?>