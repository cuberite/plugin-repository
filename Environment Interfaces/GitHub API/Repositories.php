<?php namespace GitHubAPI;
require_once 'Base.php';
require_once '../composer/vendor/autoload.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Image Helper.php';

final class Repositories
{
	use GitHubAPIProvider;

	private static function SanitiseRepositories($Repositories)
	{
		$Repositories = array_filter(
			$Repositories,
			function($Repository)
			{
				//echo var_dump(Repositories::CustomRequest('repositories', $Repository['id'], 'collaborators/' . $_SESSION['User']['Login'] . '/permission'));
				// Can't change webhooks in archived repositories
				return !($Repository['archived'] || $Repository['disabled']);
			}
		);

		usort(
			$Repositories,
			function($Lhs, $Rhs)
			{
				return strcasecmp($Lhs['name'], $Rhs['name']);
			}
		);

		return $Repositories;
	}

	public static function GetAllCurrentUserRepositories()
	{
		$Repositories = array(
			'❣ me :)' => Repositories::SanitiseRepositories(
				Repositories::GetInstance()->getReceiver(\FlexyProject\GitHub\Client::REPOSITORIES)->listYourRepositories()
			)
		);

		foreach (Repositories::CustomRequest('user', Repositories::GetInstance()->getReceiver(\FlexyProject\GitHub\Client::USERS)->getUser()['id'], 'orgs') as $Organisation)
		{
			$Repositories['🏢 ' . $Organisation['login']] = Repositories::SanitiseRepositories(
				Repositories::GetInstance()->getReceiver(\FlexyProject\GitHub\Client::REPOSITORIES)->listOrganizationRepositories($Organisation['login'])
			);
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
			$License = null;
		}

		return array($Data['name'], $Data['full_name'], $License);
	}

	public static function GetReadme($RepositoryId)
	{
		try
		{
			return base64_decode(Repositories::CustomRequest('repositories', $RepositoryId, 'readme')['content']);
		}
		catch (\Exception $NoReadme)
		{
			return null;
		}
	}

	public static function GetDescription($RepositoryId)
	{
		try
		{
			$LuaInfo = base64_decode(Repositories::CustomRequest('repositories', $RepositoryId, 'contents/Info.lua')['content']);
			return \Vlaswinkel\Lua\Lua::deserialize(explode('=', $LuaInfo, 2)[1])['Description'];
		}
		catch (\Exception $NoDescription)
		{
			return null;
		}
	}

	public static function GetScreenshots($RepositoryId)
	{
		try
		{
			return Repositories::CustomRequest('repositories', $RepositoryId, 'contents/Screenshots');
		}
		catch (\Exception $NoImages)
		{
			return array();
		}
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
			$RepositoryVersion = null;
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
		return Repositories::GetInstance()->request('/repositories/' . $RepositoryId . '/hooks', \Symfony\Component\HttpFoundation\Request::METHOD_POST, array('name' => 'web', 'active' => true, 'events' => array('push', 'release'), 'config' => array('url' => UPDATE_HOOK_ADDRESS, 'secret' => GH_OAUTH_CLIENT_SECRET)))['id'];
	}

	public static function DeleteUpdateHook($RepositoryId, $HookId)
	{
		if ($HookId === null)
		{
			// TODO: temporary until all hooks refreshed
			return;
		}

		Repositories::GetInstance()->request('/repositories/' . $RepositoryId . '/hooks/' . $HookId, \Symfony\Component\HttpFoundation\Request::METHOD_DELETE);
	}
}
?>