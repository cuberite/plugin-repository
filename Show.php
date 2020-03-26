<?php
session_start();

if (!isset($_GET['RepositoryId']))
{
	http_response_code(400);
	return;
}

require_once 'Globals.php';
require_once 'Models/Plugin.php';
require_once 'Environment Interfaces/Session.php';

$Templater = new \Twig\Environment(GetTwigLoader(), GetTwigOptions());

$Query = DB::queryFirstRow(
	'SELECT * FROM Authors, PluginData WHERE Authors.AuthorId = PluginData.AuthorId AND RepositoryId = %i',
	$_GET['RepositoryId']
);
$Downloads = DB::query('SELECT Name, Tag, Hyperlink FROM DownloadHyperlinks WHERE RepositoryId = %i', $_GET['RepositoryId']);

$Details = Session::GetLoggedInDetails();
$Templater->display('Expanded Plugin.html', array('Plugin' => $Query, 'Downloads' => $Downloads, 'LoginDetails' => $Details));
?>