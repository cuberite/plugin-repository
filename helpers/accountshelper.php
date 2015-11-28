<?php
require_once 'helpers/githubapihelper.php';

class AccountsHelper
{
	static function GetLoggedInDetails(&$Details = null)
	{
		if (!isset($_SESSION['OAuthToken']))
		{
			return false;
		}
		else
		{
			$Client = GitHubAPI::GetInstance();
			$Client->authenticate($_SESSION['OAuthToken'], '', $Client::AUTH_HTTP_TOKEN);
			
			try
			{
				$Profile = $Client->api('me')->show();
			}
			catch (Github\Exception\RuntimeException $AuthenticationError)
			{
				session_unset();
				session_destroy();
				return false;
			}
			
			$Details = array($Profile['id'], $Profile['login'], $Profile['name'], $Profile['avatar_url']);
			return true;
		}
	}
	
	static function GetDetailsFromID($ID)
	{
		$Profile = GitHubAPI::CustomRequest('user', $ID);		
		return array($Profile['login'], $Profile['name'], $Profile['avatar_url']);
	}
	
	static function AuthoriseViaGitHub($Templater)
	{
		$_SESSION['OAuthState'] = hash('sha512', session_id());
		
		$Templater->SetRedirect(
			'https://github.com/login/oauth/authorize?' .
			http_build_query(
				array(
					'client_id' => GitHubAPI::OAUTH_CLIENT_ID,
					'redirect_uri' => 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'],
					'state' => $_SESSION['OAuthState']
				)
			)
		);
	}
	
	static function ExchangeGitHubToken($Templater, $AuthorisationCode)
	{
		if (
			!isset($_GET['state']) ||
			!isset($_SESSION['OAuthState']) ||
			($_GET['state'] != $_SESSION['OAuthState']))
		{
			session_unset();
			session_destroy();
		
			ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'A potential security breach was detected; your session was reset. We\'ll try again.', $Templater);
			return false;
		}
		
		$CURLInstance = curl_init('https://github.com/login/oauth/access_token');
		$Headers[] = 'Accept: application/json';
		$Headers[] = 'User-Agent: Cuberite Plugin Repository';
		
		if (isset($_SESSION['OAuthToken']))
		{
			$Headers[] = 'Authorization: Bearer ' . $_SESSION['OAuthToken'];
		}
		
		curl_setopt($CURLInstance, CURLOPT_HTTPHEADER, $Headers);
		curl_setopt($CURLInstance, CURLOPT_CAINFO, getcwd() . '/ca-bundle.crt');
		curl_setopt($CURLInstance, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($CURLInstance, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt(
			$CURLInstance,
			CURLOPT_POSTFIELDS,
			http_build_query(
				array(
					'client_id' => GitHubAPI::OAUTH_CLIENT_ID,
					'client_secret' => GitHubAPI::OAUTH_CLIENT_SECRET,
					'redirect_uri' => 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'],
					'state' => $_SESSION['OAuthState'],
					'code' => $AuthorisationCode
				)
			)
		);		
		
		$Response = json_decode(curl_exec($CURLInstance));
		if (isset($Response->access_token))
		{
			$_SESSION['OAuthToken'] = $Response->access_token;
			return true;
		}
				
		ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'We couldn\'t verify your code with GitHub, retrying...', $Templater);
		return false;
	}
}
?>