<?php
require_once '../composer/vendor/autoload.php';

require_once 'Globals.php';
require_once 'Environment Interfaces/meekrodb.php';
require_once 'Environment Interfaces/Cache.php';
require_once 'Environment Interfaces/GitHub API/Repositories.php';
require_once 'Environment Interfaces/GitHub API/Users.php';

final class ExpandedPluginItem
{
	public function __construct($RepositoryID, $AuthorID, $DownloadCount)
	{
		$AuthorDetails = GitHubAPI\Users::GetDetailsFromID($AuthorID);
		$this->AuthorDisplayName = is_null($AuthorDetails['name']) ? $AuthorDetails['login'] : $AuthorDetails['name'];
		$this->RepositoryID = $RepositoryID;
		$this->DownloadCount = $DownloadCount;
		$this->RepositoryReadme = (new \Parsedown())->text(GitHubAPI\Repositories::GetReadme($RepositoryID));
		$this->Screenshots = GitHubAPI\Repositories::GetScreenshots($RepositoryID);
		
		list($this->RepositoryVersion, $this->PluginDownload) = GitHubAPI\Repositories::GetReleases($RepositoryID);
		list($this->RepositoryName, $this->RepositoryFullName, $this->RepositoryOwnerName, $this->License) = GitHubAPI\Repositories::GetMetadata($RepositoryID);
		list($this->IconHyperlink, $this->DominantRGB, $this->TextRGB) = GitHubAPI\Repositories::GetIconData($RepositoryID);
	}
	
	public $RepositoryID;
	public $RepositoryName;
	public $RepositoryFullName;
	public $RepositoryOwnerName;
	public $RepositoryVersion;
	public $PluginDownload;
	public $DownloadCount;
	public $AuthorDisplayName;
	public $License;
	public $RepositoryReadme;
	public $DominantRGB;
	public $TextRGB;
	public $IconHyperlink;
	public $Screenshots;
}

final class ExpandedPluginModuleGenerator
{
	public static function GenerateAndCache($RepositoryID)
	{
		$Templater = new Twig_Environment(new Twig_Loader_Filesystem('Templates/Modules'));
		$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);		
		$Response = $SQLLink->queryFirstRow('SELECT * FROM PluginData WHERE RepositoryID = %i', $RepositoryID);
		
		Cache::UpdateCacheEntry(
			CacheType::ExpandedPlugins,
			$RepositoryID . '.html',
			$Templater->render(
				'Expanded Plugin.html',
				array('Plugin' => new ExpandedPluginItem($Response['RepositoryID'], $Response['AuthorID'], $Response['DownloadCount']))
			)
		);
	}
}
?>