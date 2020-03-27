<?php
final class PluginGenerator
{
	public static function GenerateAndStore($RepositoryId, $AuthorId)
	{
		$Description = GitHubAPI\Repositories::GetDescription($RepositoryId);
		$Readme = (new \Parsedown())->text(GitHubAPI\Repositories::GetReadme($RepositoryId));
		$ScreenshotFiles = GitHubAPI\Repositories::GetScreenshots($RepositoryId);

		list($RepositoryVersion, $Releases) = GitHubAPI\Repositories::GetReleases($RepositoryId);
		list($RepositoryName, $RepositoryFullName, $License) = GitHubAPI\Repositories::GetMetadata($RepositoryId);
		list($IconHyperlink, $DominantRGB, $TextRGB) = GitHubAPI\Repositories::GetIconData($RepositoryId);

		$ScreenshotHyperlinks = array_map(
			function($File) use ($RepositoryId)
			{
				// Synced with database names
				return array('RepositoryId' => $RepositoryId, 'Name' => $File['name'], 'Hyperlink' => $File['download_url']);
			},
			$ScreenshotFiles
		);

		$DownloadHyperlinks = array_map(
			function($Release) use ($RepositoryId)
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
		if ($AuthorId === null)
		{
			DB::update(
				'PluginData',
				array(
					'RepositoryName' => $RepositoryName,
					'RepositoryFullName' => $RepositoryFullName,
					'RepositoryVersion' => $RepositoryVersion,
					'License' => $License,
					'Description' => $Description,
					'Readme' => $Readme,
					'IconHyperlink' => $IconHyperlink,
				),
				'RepositoryId = %i', $RepositoryId
			);
		}
		else
		{
			DB::insert(
				'PluginData',
				array(
					'RepositoryId' => $RepositoryId,
					'AuthorId' => $AuthorId,
					'RepositoryName' => $RepositoryName,
					'RepositoryFullName' => $RepositoryFullName,
					'RepositoryVersion' => $RepositoryVersion,
					'License' => $License,
					'Description' => $Description,
					'Readme' => $Readme,
					'IconHyperlink' => $IconHyperlink,
				)
			);
		}
		DB::delete('DownloadHyperlinks', 'RepositoryId = %i', $RepositoryId);
		DB::insert('DownloadHyperlinks', $DownloadHyperlinks);
		DB::delete('ScreenshotHyperlinks', 'RepositoryId = %i', $RepositoryId);
		if (!empty($ScreenshotHyperlinks))
		{
			// Seems like a defect in MeekroDB XD
			DB::insert('ScreenshotHyperlinks', $ScreenshotHyperlinks);
		}
		DB::commit();
	}

	public static function UpdateWebhook($RepositoryId, $HookId)
	{
		DB::update('PluginData', array('UpdateHookId' => $HookId), 'RepositoryId = %i', $RepositoryId);
	}
}
?>