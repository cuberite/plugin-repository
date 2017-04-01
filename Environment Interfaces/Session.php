<?php
require_once '../composer/vendor/autoload.php';
require_once 'Globals.php';
require_once 'Environment Interfaces/Cache.php';
require_once 'Environment Interfaces/GitHub API/Users.php';

final class LoggedInAccountDetails
{
	public $LoggedIn = true;
	public $User;

	public function __construct($Details)
	{
		$this->User = $Details;
	}
}

final class NotLoggedInAccountDetails
{
	public function __construct()
	{
		$this->LoginRedirectURL = http_build_query(array('login' => 1, 'redirect' => $_SERVER['REQUEST_URI']));
	}

	public $LoggedIn = false;
	public $LoginRedirectURL;
}

final class Session
{
	static function GetLoggedInDetails(&$Details = null)
	{
		if (!isset($_SESSION['OAuthToken']))
		{
			$Details = new NotLoggedInAccountDetails();
			return false;
		}

		if (!isset($_SESSION['UserID']))
		{
			session_unset();
			session_destroy();

			$Details = new NotLoggedInAccountDetails();
			return false;
		}

		$Details = new LoggedInAccountDetails(unserialize(Cache::GetCacheEntry(CacheType::Users, $_SESSION['UserID'])));
		return true;
	}

	static function AuthoriseViaGitHub($AdditionalParameters)
	{
		$_SESSION['OAuthState'] = hash('sha512', session_id());

		SetRedirect(
			'https://github.com/login/oauth/authorize?' .
			http_build_query(
				array(
					'client_id' => GH_OAUTH_CLIENT_ID,
					'redirect_uri' => 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . $AdditionalParameters,
					'scope' => 'admin:repo_hook',
					'state' => $_SESSION['OAuthState']
				)
			)
		);
	}

	static function ExchangeGitHubToken($AuthorisationCode)
	{
		if (
			!isset($_GET['state']) ||
			!isset($_SESSION['OAuthState']) ||
			($_GET['state'] != $_SESSION['OAuthState'])
		)
		{
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
		curl_setopt($CURLInstance, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($CURLInstance, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt(
			$CURLInstance,
			CURLOPT_POSTFIELDS,
			http_build_query(
				array(
					'client_id' => GH_OAUTH_CLIENT_ID,
					'client_secret' => GH_OAUTH_CLIENT_SECRET,
					'state' => $_SESSION['OAuthState'],
					'code' => $AuthorisationCode
				)
			)
		);

		$Response = json_decode(curl_exec($CURLInstance));
		if (isset($Response->access_token))
		{
			$_SESSION['OAuthToken'] = $Response->access_token;
			$User = GitHubAPI\Users::GetInstance()->getReceiver(\FlexyProject\GitHub\Client::USERS)->getUser();
			$_SESSION['UserID']	= $User['id'];
			Cache::UpdateCacheEntry(CacheType::Users, $User['id'], serialize($User));

			return true;
		}

		return false;
	}
}
?>