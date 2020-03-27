<?php
session_start();

require_once 'Globals.php';
require_once 'Environment Interfaces/Session.php';

if (!isset($_GET['RepositoryId']) || !isset($_POST['Submit']))
{
	http_response_code(400);
	return;
}

$Details = Session::GetLoggedInDetails();
if (!$Details->LoggedIn)
{
	SetRedirect(WebURI::Login . '?' . http_build_query(array('login' => 1, 'redirect' => $_SERVER['REQUEST_URI'])));
	return;
}

DB::insert(
	'Comments',
	array(
		'RepositoryId' => $_GET['RepositoryId'],
		'Comment' => $_POST['Comment'],
		'AuthorID' => $Details->User->AuthorId
	)
);
// TODO: catch exceptions

SetRedirect(WebURI::Show . '/' . $_GET['RepositoryId']);
?>