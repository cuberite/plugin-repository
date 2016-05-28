<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';
require_once 'templates/immersiveform.php';
require_once 'helpers/accountshelper.php';

if (isset($_GET['logout']) && $_GET['logout'])
{
	session_unset();
	session_destroy();

	$Template->SetRedirect();
	return;
}

if (isset($_GET['login']) && $_GET['login'])
{
	AccountsHelper::AuthoriseViaGitHub($Template);
	return;
}

if (isset($_GET['code']))
{
	if (!AccountsHelper::ExchangeGitHubToken($Template, $_GET['code']))
	{
		$Template->SetRefresh($_SERVER['PHP_SELF'] . '?login=1');
		return;
	}
}

$Template->SetRedirect();
return;
?>
