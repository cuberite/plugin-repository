<?php
session_start();

require_once 'Globals.php';
require_once 'Environment Interfaces/Session.php';

$Templater = new \Twig\Environment(GetTwigLoader(), GetTwigOptions());

$Plugins = DB::query(
	'SELECT RepositoryId, DownloadCount, RepositoryName, RepositoryVersion, Description, IconHyperlink, Login, DisplayName
	FROM Authors, PluginData WHERE Authors.AuthorId = PluginData.AuthorId ORDER BY DownloadCount DESC'
);
$Counts = array_column(DB::query('SELECT RepositoryId, COUNT(*) AS Count FROM Comments GROUP BY RepositoryId'), 'Count', 'RepositoryId');

$Details = Session::GetLoggedInDetails();
$Templater->display('Condensed Plugins.html', array('Plugins' => $Plugins, 'CommentCounts' => $Counts, 'LoginDetails' => $Details));
?>