<?php
session_start();

if (!isset($_GET['query']))
{
	http_response_code(400);
	return;
}

require_once 'Globals.php';
require_once 'Environment Interfaces/Session.php';

$Templater = new \Twig\Environment(GetTwigLoader(), GetTwigOptions());

$Plugins = DB::query(
	'SELECT RepositoryId, RepositoryName, RepositoryVersion, DisplayName, License, Description, IconHyperlink
	FROM Authors, PluginData WHERE Authors.AuthorId = PluginData.AuthorId AND
	(DisplayName LIKE %ss0 OR RepositoryFullName LIKE %ss0 OR RepositoryName LIKE %ss0 OR RepositoryId = %i0 OR Description LIKE %ss0)',
	$_GET['query']
);

$Details = Session::GetLoggedInDetails();
$Templater->display(
	'Condensed Plugins.html',
	array(
		'ActiveItems' => array('', 'active'),
		'SearchedText' => $_GET['query'],
		'Plugins' => $Plugins,
		'LoginDetails' => $Details
	)
);
?>