<?php
session_start();

require_once 'Globals.php';

if (!isset($_GET['RepositoryId']) || !isset($_POST['Download']) || !isset($_POST['DownloadType']))
{
	http_response_code(400);
	return;
}

if (DB::update('PluginData', array('DownloadCount' => DB::sqleval('DownloadCount + 1')), 'RepositoryId = %i', $_GET['RepositoryId']) === false)
{
	http_response_code(404);
	return;
}

SetRedirect($_POST['DownloadType']);
?>