<?php namespace GitHubAPI;
require_once '../composer/vendor/autoload.php';

trait GitHubAPIProvider
{
	private function __construct()
	{
	}

	private function __clone()
	{
	}

	public static function GetInstance()
	{
		static $Instance = null;
		if ($Instance === null)
		{
			$Instance = new \FlexyProject\GitHub\Client();

			$Instance->setClientId(GH_OAUTH_CLIENT_ID);
			$Instance->setClientSecret(GH_OAUTH_CLIENT_SECRET);

			if (isset($_SESSION['OAuthToken']))
			{
				// The presence of a session token is atomic to script executions.
				// Therefore, this presence of this token will not change in the lifetime of this Client object.
				// All calls to GetInstance will evaluate this condition identically, and so it is safely placed within the first-init block.
				$Instance->setToken($_SESSION['OAuthToken'], \FlexyProject\GitHub\Client::OAUTH2_HEADER_AUTH);
			}
		}
		return $Instance;
	}

	public static function CustomRequest($Prefix, $QueryID, $Postfix = '')
	{
		return GitHubAPIProvider::GetInstance()->request(
			'/' . $Prefix .
			'/' . $QueryID .
			(empty($Postfix) ? '' : '/') . $Postfix
		);
	}
}
?>