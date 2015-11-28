<?php

require_once 'helpers/accountshelper.php';
require_once 'helpers/imagehelper.php';
require_once 'helpers/githubapihelper.php';
require_once '../composer/vendor/autoload.php';

class PluginItemTemplate
{
	static function AddCondensedPluginItem($MinimumAnimationDuration, $SQLEntry, $Templater)
	{
		$PluginVersion;
		$IconHyperlink;
		$Data = GitHubAPI::CustomRequest('repositories', $SQLEntry['RepositoryID']);
		list($AuthorName, $AuthorDisplayName) = AccountsHelper::GetDetailsFromID($SQLEntry['AuthorID']);
		
		try
		{
			$PluginVersion = GitHubAPI::CustomRequest('repositories', $Data['id'], 'releases/latest')['tag_name'];
		}
		catch (Exception $NoVersion)
		{
			$PluginVersion = false;
		}
		
		try
		{
			$IconHyperlink = GitHubAPI::CustomRequest('repositories', $Data['id'], 'Plugin Repository/favicon.png')['download_url'];
		}
		catch (Exception $NoVersion)
		{
			$IdenticonGenerator = new Identicon\Identicon();
			$IconHyperlink = $IdenticonGenerator->getImageDataUri($Data['name'], 60);
		}
		
		$DominantRGB;
		$TextRGB;
		ImageHelper::GetDominantColorAndTextColour($IconHyperlink, $DominantRGB, $TextRGB);
	
		$Templater->BeginTag('a', array('style' => 'text-decoration: none', 'href' => 'showplugin.php?id=' . $Data['id']));
			$Templater->BeginTag('div', array('class' => 'boundedbox plugin showcondensed', 'style' => 'background-color:' . $DominantRGB . '; color:' . $TextRGB . '; animation-duration:' . $MinimumAnimationDuration . 'ms;'));
				$Templater->BeginTag('img', array('class' => 'boundedbox condensedicon show', 'src' => $IconHyperlink), true);
				$Templater->BeginTag('figcaption', array('class' => 'boundedbox condensedicon caption'));
					$Templater->BeginTag('b');
						$Templater->Append($Data['name']);
					$Templater->EndLastTag();
					$Templater->BeginTag('br', array(), true);
					$Templater->BeginTag('div', array('class' => 'boundedbox condensedicon caption description'));
						$Templater->Append('Author: ' . $AuthorDisplayName);
						$Templater->BeginTag('br', array(), true);
						if ($PluginVersion)
						{
							$Templater->Append('Version: ' . $PluginVersion);
						}
					$Templater->EndLastTag();
				$Templater->EndLastTag();
			$Templater->EndLastTag();
		$Templater->EndLastTag();
	}

	static function AddExpandedPluginItem($SQLEntry, $Templater)
	{
		$PluginVersion = false;
		$Data = GitHubAPI::CustomRequest('repositories', $SQLEntry['RepositoryID']);
		list($AuthorName, $AuthorDisplayName) = AccountsHelper::GetDetailsFromID($SQLEntry['AuthorID']);
		
		try
		{
			$PluginVersion = GitHubAPI::CustomRequest('repositories', $Data['id'], 'releases/latest')['tag_name'];
		}
		catch (Exception $NoVersion)
		{
			$PluginVersion = false;
		}
		
		try
		{
			$IconHyperlink = GitHubAPI::CustomRequest('repositories', $Data['id'], 'Plugin Repository/favicon.png')['download_url'];
		}
		catch (Exception $NoVersion)
		{
			$IdenticonGenerator = new Identicon\Identicon();
			$IconHyperlink = $IdenticonGenerator->getImageDataUri($Data['name'], 150);
		}
		
		$Templater->BeginTag('article', array('class' => 'boundedbox plugin show infobox'));
			$Templater->BeginTag('nav');
				$Templater->BeginTag('a', array('href' => 'https://api.github.com/repos/' . $Data['full_name'] . '/zipball'));
					$Templater->BeginTag('img', array('src' => 'images/download.svg', 'class' => 'download', 'alt' => 'Download button', 'title' => 'Download'), true);
				$Templater->EndLastTag();
				if (AccountsHelper::GetLoggedInDetails($Details))
				{
					$Templater->BeginTag('a', array('href' => '#comments'));
						$Templater->BeginTag('img', array('src' => 'images/review.svg', 'class' => 'review', 'alt' => 'Review button', 'title' => 'Review'), true);
					$Templater->EndLastTag();
					$Templater->BeginTag('a', array('href' => '#comments'));
						$Templater->BeginTag('img', array('src' => 'images/rate.svg', 'class' => 'rate', 'alt' => 'Rate button', 'title' => 'Rate'), true);
					$Templater->EndLastTag();
					$Templater->BeginTag('a', array('href' => '#comments'));
						$Templater->BeginTag('img', array('src' => 'images/favourite.svg', 'class' => 'favourite', 'alt' => 'Favourite button', 'title' => 'Favourite'), true);
					$Templater->EndLastTag();
					$Templater->BeginTag('a', array('href' => '#comments'));
						$Templater->BeginTag('img', array('src' => 'images/report.svg', 'class' => 'report', 'alt' => 'Report button', 'title' => 'Report'), true);
					$Templater->EndLastTag();
					
					if ($Details[0] == $SQLEntry['AuthorID'])
					{
						$Templater->BeginTag('a', array('href' => 'edit.php?id=' . $SQLEntry['RepositoryID']));
							$Templater->BeginTag('img', array('src' => 'images/edit.svg', 'class' => 'edit', 'alt' => 'Edit button', 'title' => 'Edit'), true);
						$Templater->EndLastTag();
					}
				}
			$Templater->EndLastTag();

			$Templater->BeginTag('img', array('class' => 'boundedbox expandedicon show', 'src' => $IconHyperlink), true);
			$Templater->BeginTag('figcaption', array('class' => 'boundedbox expandedicon caption'));
				$Templater->BeginTag('h2');
					$Templater->Append($Data['name']);
				$Templater->EndLastTag();
				
				$Templater->Append('Author: ' . $AuthorDisplayName . ' (' . $Data['owner']['login'] . ')');
				
				if ($PluginVersion)
				{
					$Templater->BeginTag('br', array(), true);
					$Templater->Append('Version: ' . $PluginVersion);
					$Templater->BeginTag('br', array(), true);
					$Templater->Append('Rating: ' . $PluginVersion);
					$Templater->BeginTag('br', array(), true);
					$Templater->Append('Downloads: ' . $PluginVersion);
					$Templater->BeginTag('br', array(), true);
					$Templater->Append('Category: ' . $PluginVersion);
				}
			$Templater->EndLastTag();
			$Templater->BeginTag('hr', array(), true);

			$ImageFound = false;//ImageHelper::DisplaySerialisedImages($SQLEntry['Images'], $Templater);
			if ($ImageFound)
			{
				$Templater->BeginTag('hr', array(), true);
			}
			$Templater->BeginTag('p');
				try
				{
					$Parser = new Parsedown();
					$Templater->Append(
						$Parser->text(
							base64_decode(
								GitHubAPI::CustomRequest(
									'repositories',
									$SQLEntry['RepositoryID'],
									'readme'
								)['content']
							)
						)
					);					
				}
				catch (Exception $NoReadme)
				{
					$Templater->Append('(the plugin author has not provided a README file)');
				}
			$Templater->EndLastTag();
		$Templater->EndLastTag();
	}
}
?>