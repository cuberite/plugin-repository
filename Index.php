<?php
session_start();

require_once 'Globals.php';
require_once 'Environment Interfaces/Session.php';

$Templater = new \Twig\Environment(GetTwigLoader(), GetTwigOptions());

$Plugins = DB::query(
	'SELECT RepositoryId, RepositoryName, RepositoryVersion, DisplayName, License, Description, IconHyperlink
	FROM Authors, PluginData WHERE Authors.AuthorId = PluginData.AuthorId'
);

$Details = Session::GetLoggedInDetails();
$Templater->display('Condensed Plugins.html', array('Plugins' => $Plugins, 'LoginDetails' => $Details));
?>