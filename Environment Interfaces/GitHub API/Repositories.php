<?php namespace GitHubAPI;
require_once 'Base.php';
require_once '../composer/vendor/autoload.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Image Helper.php';

final class Repositories
{
	use GitHubAPIProvider;

	public static function GetAllCurrentUserRepositories()
	{
		$Repositories = array('❣ me :)' => Repositories::GetInstance()->getReceiver(\FlexyProject\GitHub\Client::REPOSITORIES)->listYourRepositories());

		foreach (Repositories::CustomRequest('user', Repositories::GetInstance()->getReceiver(\FlexyProject\GitHub\Client::USERS)->getUser()['id'], 'orgs') as $Organisation)
		{
			$RepositoryGroup = Repositories::GetInstance()->getReceiver(\FlexyProject\GitHub\Client::REPOSITORIES)->listOrganizationRepositories($Organisation['login']);
			usort(
				$RepositoryGroup,
				function($Lhs, $Rhs)
				{
					return strcasecmp($Lhs['name'], $Rhs['name']);
				}
			);
			$Repositories['🏢 ' . $Organisation['login']] = $RepositoryGroup;
		}

		return $Repositories;
	}

	public static function GetMetadata($RepositoryId)
	{
		try
		{
			$Data = Repositories::CustomRequest('repositories', $RepositoryId);
		}
		catch (\Exception $e)
		{
			http_response_code(500);
			die("Fatal error - failed to retrieve required repository metadata.");
		}

		if (isset($Data['license']))
		{
			$License = $Data['license']['name'];
		}
		else
		{
			$License = false;
		}

		return array($Data['name'], $Data['full_name'], $License);
	}

	public static function GetReadme($RepositoryId)
	{
		try
		{
			$Readme = base64_decode(Repositories::CustomRequest('repositories', $RepositoryId, 'readme')['content']);
		}
		catch (\Exception $NoReadme)
		{
			$Readme = '(the plugin author has not provided a readme)';
		}

		return $Readme;
	}

	public static function GetDescription($RepositoryId)
	{
		try
		{
			$LuaInfo = base64_decode(Repositories::CustomRequest('repositories', $RepositoryId, 'contents/Info.lua')['content']);
			$Description = \Vlaswinkel\Lua\Lua::deserialize(explode('=', $LuaInfo, 2)[1])['Description'];
		}
		catch (\Exception $NoDescription)
		{
			$Description = false;
		}

		return $Description;
	}

	public static function GetScreenshots($RepositoryId)
	{
		try
		{
			$Screenshots = Repositories::CustomRequest('repositories', $RepositoryId, 'contents/Screenshots');
		}
		catch (\Exception $NoImages)
		{
			$Screenshots = false;
		}

		return $Screenshots;
	}

	public static function GetReleases($RepositoryId)
	{
		try
		{
			$LatestRelease = Repositories::CustomRequest('repositories', $RepositoryId, 'releases/latest');
			$RepositoryVersion = $LatestRelease['tag_name'];
			$Releases = Repositories::CustomRequest('repositories', $RepositoryId, 'releases');
		}
		catch (\Exception $NoVersion)
		{
			// TODO: NULL / empty instead?
			$RepositoryVersion = false;
			$Releases = array();
		}

		return array($RepositoryVersion, $Releases);
	}

	public static function GetIconData($RepositoryId)
	{
		$WasIdenticon = false;
		try
		{
			$IconHyperlink = Repositories::CustomRequest('repositories', $RepositoryId, 'contents/Favicon.png')['download_url'];
		}
		catch (\Exception $NoIcon)
		{
			$WasIdenticon = true;
			$IdenticonGenerator = new \Identicon\Identicon();
			$IconHyperlink = $IdenticonGenerator->getImageDataUri(Repositories::GetMetadata($RepositoryId)[0], 150);
		}

		$DominantRGB;
		$TextRGB;

		if ($WasIdenticon)
		{
			\ImageHelper::GetDominantColorAndTextColour(base64_decode(substr($IconHyperlink, 22)), $DominantRGB, $TextRGB);
			// Where 22 is the exact length of data:image/png;base64,
			// Exact since we're putting the length into a zero-based index
		}
		else
		{
			\ImageHelper::GetDominantColorAndTextColour(file_get_contents($IconHyperlink), $DominantRGB, $TextRGB);
		}

		return array($IconHyperlink, $DominantRGB, $TextRGB);
	}

	public static function CreateUpdateHook($RepositoryId)
	{
		return Repositories::GetInstance()->request('/repositories/' . $RepositoryId . '/hooks', \Symfony\Component\HttpFoundation\Request::METHOD_POST, array('name' => 'web', 'active' => true, 'events' => array('push', 'release'), 'config' => array('url' => 'https://cuberiteplugins.azurewebsites.net/processhook', 'secret' => GH_OAUTH_CLIENT_SECRET)))['id'];
	}

	public static function DeleteUpdateHook($RepositoryId, $HookId)
	{
		Repositories::GetInstance()->request('/repositories/' . $RepositoryId . '/hooks/' . $HookId, \Symfony\Component\HttpFoundation\Request::METHOD_DELETE);
	}
}
?>