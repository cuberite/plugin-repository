<?php
require_once '../composer/vendor/autoload.php';

use Github\HttpClient\Message\ResponseMediator;

final class GitHubAPI
{
	const OAUTH_CLIENT_ID = "69910aa840cf39d66311";
	const OAUTH_CLIENT_SECRET = "51634cd4c8225dab2b75eb6a6e5659bd4c88da38";
	
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
	
	public static function CustomRequest($Prefix, $QueryID, $Postfix = '')
	{
		return ResponseMediator::getContent(
			GitHubAPI::GetInstance()->getHttpClient()->get(
				'/' . $Prefix .
				'/' . $QueryID .
				(empty($Postfix) ? '' : '/') . $Postfix .
				'?client_id=' . GitHubAPI::OAUTH_CLIENT_ID .
				'&client_secret=' . GitHubAPI::OAUTH_CLIENT_SECRET
			)
		);		
	}
	
	public static function GetAllUserRepositories($CurrentUser)
	{
		$Client = GitHubAPI::GetInstance();
		$Repositories = array('❣ me :)' => $CurrentUser->repositories());
		
		foreach (GitHubAPI::CustomRequest('user', $CurrentUser->show()['id'], 'orgs') as $Organisation)
		{
			$Repositories['🏢 ' . $Organisation['login']] = GitHubAPI::CustomRequest('orgs', $Organisation['login'], 'repos');
		}
		
		return $Repositories;
	}
}
?>