<?php
require_once '../composer/vendor/autoload.php';

final class GitHubAPI
{
	private function __construct()
	{
	}
	
	private function __clone()
	{
	}
	
	private static function GetCacheInstance()
	{
		static $Instance = null;
		if ($Instance === null)
		{
			$Instance = new \Github\HttpClient\CachedHttpClient(array('cache_dir' => '../github-api-cache'));
		}
		return $Instance;		
	}
	
	public static function GetInstance()
	{
		static $Instance = null;
		if ($Instance === null)
		{
			$Instance = new \Github\Client(GitHubAPI::GetCacheInstance());
		}
		return $Instance;
	}
	
	public static function GetRepositoryData($ID)
	{
		return \Github\HttpClient\Message\ResponseMediator::getContent(GitHubAPI::GetInstance()->getHttpClient()->get('/repositories/' . $ID));
	}
	
	public static function GetUserData($ID)
	{
		return \Github\HttpClient\Message\ResponseMediator::getContent(GitHubAPI::GetInstance()->getHttpClient()->get('/user/' . $ID));
	}
}
?>