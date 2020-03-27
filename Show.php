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

$Query = DB::queryFirstRow(
	'SELECT * FROM Authors, PluginData WHERE Authors.AuthorId = PluginData.AuthorId AND RepositoryId = %i',
	$_GET['RepositoryId']
);

if ($Query === null)
{
	http_response_code(404);
	return;
}

$Templater = new \Twig\Environment(GetTwigLoader(), GetTwigOptions());
$Downloads = DB::query('SELECT Name, Tag, Hyperlink FROM DownloadHyperlinks WHERE RepositoryId = %i', $_GET['RepositoryId']);
$Screenshots = DB::query('SELECT Name, Hyperlink FROM ScreenshotHyperlinks WHERE RepositoryId = %i', $_GET['RepositoryId']);
$Comments = DB::query(
	'SELECT Comment, CreationTime, DisplayName, AvatarHyperlink
	FROM Authors, Comments WHERE Authors.AuthorId = Comments.AuthorId AND RepositoryId = %i', $_GET['RepositoryId']
);

$Details = Session::GetLoggedInDetails();
$Templater->display(
	'Expanded Plugin.html',
	array(
		'Plugin' => $Query,
		'Downloads' => $Downloads,
		'Screenshots' => $Screenshots,
		'Comments' => $Comments,
		'LoginDetails' => $Details
	)
);
?>