<?php
session_start();

require_once 'Globals.php';
require_once 'Environment Interfaces/Session.php';

if (isset($_GET['logout']) && $_GET['logout'])
{
	session_unset();
	session_destroy();

	SetRedirect();
	return;
}

$HasRedirect = isset($_GET['redirect']);

if (isset($_GET['login']) && $_GET['login'])
{
	Session::AuthoriseViaGitHub(
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
	if (!Session::ExchangeGitHubToken($_GET['code']))
	{
		SetRefresh(
			$_SERVER['PHP_SELF'] .
			'?' .
			http_build_query(
				array(
					'login' => 1,
					'redirect' => $HasRedirect ? $_GET['redirect'] : null
				)
			)
		);

		// TODO: better response

		session_unset();
		session_destroy();

		http_response_code(500);
		return;
	}

	if ($HasRedirect)
	{
		SetRedirect('https://' . $_SERVER['SERVER_NAME'] . urldecode($_GET['redirect']));
		return;
	}
}

http_response_code(400);
?>