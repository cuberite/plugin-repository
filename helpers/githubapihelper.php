<?php
require_once '../composer/vendor/autoload.php';
require_once 'cachehelper.php';
require_once 'imagehelper.php';

final class GitHubAPI
{	
	const METADATA_CACHE_FILE_NAME = 'Metadata';
	const ICONDATA_CACHE_FILE_NAME = 'IconData';
	const README_CACHE_FILE_NAME = 'ReadMe';
	const USER_CACHE_FILE_NAME = 'User';
	
	private function __construct()
	{
	}
	
	private function __clone()
	{
	}
	
	private static function GetCacheInstance()
	{
		/*
		static $Instance = null;
		if ($Instance === null)
		{
			$Instance = new \Github\HttpClient\CachedHttpClient(array('cache_dir' => '../github-api-cache'));
		}
		return $Instance;
		*/
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
		return GitHubAPI::GetInstance()->request(
			'/' . $Prefix .
			'/' . $QueryID .
			(empty($Postfix) ? '' : '/') . $Postfix
		);		
	}
	
	public static function GetAllCurrentUserRepositories()
	{
		$Repositories = array('❣ me :)' => GitHubAPI::GetInstance()->getReceiver(\FlexyProject\GitHub\Client::REPOSITORIES)->listYourRepositories());
		
		foreach (GitHubAPI::CustomRequest('user', GitHubAPI::GetInstance()->getReceiver(\FlexyProject\GitHub\Client::USERS)->getUser()['id'], 'orgs') as $Organisation)
		{
			$Repositories['🏢 ' . $Organisation['login']] = GitHubAPI::CustomRequest('orgs', $Organisation['login'], 'repos');
		}
		
		return $Repositories;
	}
	
	public static function CreateRepositoryUpdateHook($RepositoryID)
	{
		return GitHubAPI::GetInstance()->request('/repositories/' . $RepositoryID . '/hooks', \Symfony\Component\HttpFoundation\Request::METHOD_POST, array('name' => 'web', 'active' => true, 'events' => array('push', 'release'), 'config' => array('url' => 'https://cuberiteplugins.azurewebsites.net/processhook.php', 'secret' => GH_OAUTH_CLIENT_SECRET)))['id'];
	}
	
	public static function DeleteRepositoryUpdateHook($RepositoryID, $HookID)
	{
		GitHubAPI::GetInstance()->request('/repositories/' . $RepositoryID . '/hooks/' . $HookID, \Symfony\Component\HttpFoundation\Request::METHOD_DELETE);
	}
	
	public static function ProcessRepositoryProperties($RepositoryID)
	{
		$PluginVersion;
		$IconHyperlink;
		$Data = GitHubAPI::CustomRequest('repositories', $RepositoryID);	
		
		try
		{
			$Readme = base64_decode(GitHubAPI::CustomRequest('repositories', $RepositoryID, 'readme')['content']);
		}
		catch (Exception $NoReadme)
		{
			$Readme = '(the plugin author has not provided a readme)';
		}
		
		try
		{
			$PluginVersion = GitHubAPI::CustomRequest('repositories', $RepositoryID, 'releases/latest')['tag_name'];
		}
		catch (Exception $NoVersion)
		{
			$PluginVersion = false;
		}
		
		$WasIdenticon = false;
		try
		{
			throw new Exception;
			$IconHyperlink = GitHubAPI::CustomRequest('repositories', $RepositoryID, 'contents/Plugin Repository/favicon.png')['download_url'];
		}
		catch (Exception $NoIcon)
		{
			$WasIdenticon = true;
			$IdenticonGenerator = new Identicon\Identicon();
			$IconHyperlink = $IdenticonGenerator->getImageDataUri($Data['name'], 150);
		}
				
		$DominantRGB;
		$TextRGB;
		
		if ($WasIdenticon)
		{
			ImageHelper::GetDominantColorAndTextColour(base64_decode(substr($IconHyperlink, 22)), $DominantRGB, $TextRGB);
		}
		else
		{
			ImageHelper::GetDominantColorAndTextColour(file_get_contents($IconHyperlink), $DominantRGB, $TextRGB);
		}
		
		RepositoryResourcesCache::UpdateCacheEntries(RepositoryResourcesCache::CACHE_TYPE_REPOSITORYDATA, $RepositoryID, GitHubAPI::METADATA_CACHE_FILE_NAME, serialize(array($Data['name'], $Data['full_name'], $Data['owner']['login'], $PluginVersion)));
		RepositoryResourcesCache::UpdateCacheEntries(RepositoryResourcesCache::CACHE_TYPE_REPOSITORYDATA, $RepositoryID, GitHubAPI::ICONDATA_CACHE_FILE_NAME, serialize(array($IconHyperlink, $DominantRGB, $TextRGB)));
		RepositoryResourcesCache::UpdateCacheEntries(RepositoryResourcesCache::CACHE_TYPE_REPOSITORYDATA, $RepositoryID, GitHubAPI::README_CACHE_FILE_NAME, $Readme);
	}
	
	public static function ProcessUserProperties($UserID)
	{
		$Profile = GitHubAPI::CustomRequest('user', $UserID);
		RepositoryResourcesCache::UpdateCacheEntries(RepositoryResourcesCache::CACHE_TYPE_USERDATA, $UserID, GitHubAPI::USER_CACHE_FILE_NAME, serialize(array($Profile['id'], $Profile['login'], $Profile['name'], $Profile['avatar_url'])));
	}
		
	public static function GetCachedUserData($UserID)
	{
		return unserialize(RepositoryResourcesCache::GetCacheEntry(RepositoryResourcesCache::CACHE_TYPE_USERDATA, $UserID, GitHubAPI::USER_CACHE_FILE_NAME));		
	}
	
	public static function GetCachedRepositoryMetadata($RepositoryID)
	{
		return unserialize(RepositoryResourcesCache::GetCacheEntry(RepositoryResourcesCache::CACHE_TYPE_REPOSITORYDATA, $RepositoryID, GitHubAPI::METADATA_CACHE_FILE_NAME));
	}
	
	public static function GetCachedRepositoryIconData($RepositoryID)
	{
		return unserialize(RepositoryResourcesCache::GetCacheEntry(RepositoryResourcesCache::CACHE_TYPE_REPOSITORYDATA, $RepositoryID, GitHubAPI::ICONDATA_CACHE_FILE_NAME));
	}
	
	public static function GetCachedRepositoryReadme($RepositoryID)
	{
		return RepositoryResourcesCache::GetCacheEntry(RepositoryResourcesCache::CACHE_TYPE_REPOSITORYDATA, $RepositoryID, GitHubAPI::README_CACHE_FILE_NAME);
	}
}
?>