<?php
require_once '../composer/vendor/autoload.php';

use Github\HttpClient\Message\ResponseMediator;

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
		return ResponseMediator::getContent(GitHubAPI::GetInstance()->getHttpClient()->get('/repositories/' . $ID));
	}
	
	public static function GetUserData($ID)
	{
		return ResponseMediator::getContent(GitHubAPI::GetInstance()->getHttpClient()->get('/user/' . $ID));
	}
	
	public static function GetAllUserRepositories($CurrentUser)
	{
		$Client = GitHubAPI::GetInstance();
		$Repositories = $CurrentUser->repositories();
		
		foreach (ResponseMediator::getContent(GitHubAPI::GetInstance()->getHttpClient()->get('/user/' . $CurrentUser->show()['id'] . '/orgs')) as $Organisation)
		{
			$Repositories = array_merge($Repositories, ResponseMediator::getContent(GitHubAPI::GetInstance()->getHttpClient()->get('/orgs/' . $Organisation['login'] . '/repos')));
		}
		
		return $Repositories;
	}
}
?>