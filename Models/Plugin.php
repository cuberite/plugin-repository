<?php
final class PluginGenerator
{
	public static function GenerateAndStore($RepositoryId, $AuthorId, $HookId)
	{
		require_once 'Environment Interfaces/GitHub API/Users.php';
		require_once 'Environment Interfaces/GitHub API/Repositories.php';

		$AuthorDetails = GitHubAPI\Users::GetDetailsFromId($AuthorId);
		$Description = GitHubAPI\Repositories::GetDescription($RepositoryId);
		$Readme = (new \Parsedown())->text(GitHubAPI\Repositories::GetReadme($RepositoryId));
		$ScreenshotHyperlinks = GitHubAPI\Repositories::GetScreenshots($RepositoryId);

		list($RepositoryVersion, $Releases) = GitHubAPI\Repositories::GetReleases($RepositoryId);
		list($RepositoryName, $RepositoryFullName, $License) = GitHubAPI\Repositories::GetMetadata($RepositoryId);
		list($IconHyperlink, $DominantRGB, $TextRGB) = GitHubAPI\Repositories::GetIconData($RepositoryId);

		$DownloadHyperlinks = array_map(
			function($Release)
			{
				// Synced with database names
				return array('RepositoryId' => $RepositoryId, 'Name' => $Release['name'], 'Tag' => $Release['tag_name'], 'Hyperlink' => $Release['zipball_url']);
			},
			$Releases
		);

		array_unshift(
			$DownloadHyperlinks,
			array(
				'RepositoryId' => $RepositoryId,
				'Name' => 'Bleeding edge',
				'Tag' => 'HEAD',
				'Hyperlink' => "https://api.github.com/repos/$RepositoryFullName/zipball"
			)
		);

		DB::startTransaction();
		DB::insertUpdate(
			'PluginData',
			array(
				'RepositoryId' => $RepositoryId,
				'AuthorId' => $AuthorId,
				'DownloadCount' => 0,
				'UpdateHookId' => $HookId,
				'RepositoryName' => $RepositoryName,
				'RepositoryFullName' => $RepositoryFullName,
				'RepositoryVersion' => $RepositoryVersion,
				'License' => $License,
				'Description' => $Description,
				'Readme' => $Readme,
				'IconHyperlink' => $IconHyperlink,
			)
		);
		DB::insertUpdate(
			'Authors',
			array(
				'AuthorId' => $AuthorId,
				'Login' => $AuthorDetails['login'],
				'DisplayName' => $AuthorDetails['name'],
				'AvatarHyperlink' => $AuthorDetails['avatar_url']
			)
		);
		DB::delete('DownloadHyperlinks', 'RepositoryId=%i', $RepositoryId);
		DB::insert('DownloadHyperlinks', $DownloadHyperlinks);
		/*DB::delete('ScreenshotHyperlinks', 'RepositoryId=%i', $RepositoryId);
		DB::insert(
			'ScreenshotHyperlinks',
			array(
				'AuthorId' => $AuthorId,
				'Login' => $AuthorDetails['login'],
				'DisplayName' => $AuthorDetails['name'],
				'AvatarHyperlink' => $AuthorDetails['avatar_url']
			)
		);*/
		DB::commit();
	}
}
?>