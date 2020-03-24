<?php
require_once '../composer/vendor/autoload.php';

require_once 'Globals.php';
require_once 'Environment Interfaces/meekrodb.php';
require_once 'Environment Interfaces/Cache.php';
require_once 'Environment Interfaces/GitHub API/Repositories.php';
require_once 'Environment Interfaces/GitHub API/Users.php';

final class CondensedPluginItem
{
	public function __construct($RepositoryID, $AuthorID, $MinimumAnimationDuration)
	{
		$AuthorDetails = GitHubAPI\Users::GetDetailsFromID($AuthorID);
		$this->AuthorDisplayName = is_null($AuthorDetails['name']) ? $AuthorDetails['login'] : $AuthorDetails['name'];
		$this->RepositoryID = $RepositoryID;
		$this->MinimumAnimationDuration = $MinimumAnimationDuration;
		$this->Description = GitHubAPI\Repositories::GetDescription($RepositoryID);

		list($this->RepositoryVersion) = GitHubAPI\Repositories::GetReleases($RepositoryID);
		list($this->RepositoryName, , , $this->License) = GitHubAPI\Repositories::GetMetadata($RepositoryID);
		list($this->IconHyperlink, $this->DominantRGB, $this->TextRGB) = GitHubAPI\Repositories::GetIconData($RepositoryID);
	}

	public $RepositoryID;
	public $RepositoryName;
	public $RepositoryVersion;
	public $AuthorDisplayName;
	public $License;
	public $Description;
	public $DominantRGB;
	public $TextRGB;
	public $IconHyperlink;
	public $MinimumAnimationDuration;
}

final class CondensedPluginModuleGenerator
{
	public static function GenerateAndCache($RepositoryID)
	{
		$Templater = new \Twig\Environment(new \Twig\Loader\FilesystemLoader('Templates/Modules'));
		$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);
		$Response = $SQLLink->queryFirstRow('SELECT * FROM PluginData WHERE RepositoryID = %i', $RepositoryID);

		$MinimumDuration = rand(100, 1000);

		Cache::UpdateCacheEntry(
			CacheType::CondensedPlugins,
			$RepositoryID . '.html',
			$Templater->render(
				'Condensed Plugin.html',
				array('Plugin' => new CondensedPluginItem($Response['RepositoryID'], $Response['AuthorID'], $MinimumDuration))
			)
		);
	}
}
?>