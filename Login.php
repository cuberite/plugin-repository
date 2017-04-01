<?php
session_start();

require_once 'functions.php';
require_once 'helpers/templater.php';
require_once 'helpers/meekrodb.php';
require_once 'templates/immersiveform.php';
require_once 'helpers/accountshelper.php';

$Template = new Templater();
$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (isset($_GET['logout']) && $_GET['logout'])
{
	session_unset();
	session_destroy();
	
	$Template->SetRedirect();
	return;
}

$HasRedirect = isset($_GET['redirect']);

if (isset($_GET['login']) && $_GET['login'])
{
	AccountsHelper::AuthoriseViaGitHub(
		$Template,
		'?' . http_build_query(
			array(
				'redirect' => $HasRedirect ? $_GET['redirect'] : null
			)
		)
	);
	return;
}

if (isset($_GET['code']))
{
	if (!AccountsHelper::ExchangeGitHubToken($Template, $_GET['code']))
	{
		$Template->SetRefresh(
			$_SERVER['PHP_SELF'] .
			'?' .
			http_build_query(
				array(
					'login' => 1,
					'redirect' => $HasRedirect ? $_GET['redirect'] : null
				)
			)
		);
		return;
	}
	
	if ($HasRedirect)
	{
		$Template->SetRedirect('https://' . $_SERVER['SERVER_NAME'] . urldecode($_GET['redirect']));
		return;
	}
}

$Template->SetRedirect();
return;
?>