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
		$Client = GitHubAPI::GetInstance();
		$Data = GitHubAPI::GetRepositoryData($SQLEntry['RepositoryID']);
		list($AuthorName, $AuthorDisplayName) = AccountsHelper::GetDetailsFromID($SQLEntry['AuthorID']);
		
		try
		{
			$PluginVersion = $Client->api('repo')->releases()->latest($AuthorName, $Data['name'])['tag_name'];
		}
		catch (Exception $NoVersion)
		{
			$PluginVersion = false;
		}
		
		try
		{
			$IconHyperlink = $Client->api('repo')->contents()->show($AuthorName, $Data['name'], 'Plugin Repository/favicon.png')['download_url'];
		}
		catch (Exception $NoVersion)
		{
			$IconHyperlink = 'http://identicon.org/?t=' . $Data['name'];
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
		$Client = GitHubAPI::GetInstance();
		$Data = GitHubAPI::GetRepositoryData($SQLEntry['RepositoryID']);
		list($AuthorName, $AuthorDisplayName) = AccountsHelper::GetDetailsFromID($SQLEntry['AuthorID']);
		
		try
		{
			$PluginVersion = $Client->api('repo')->releases()->latest($AuthorName, $Data['name'])['tag_name'];
		}
		catch (Exception $NoVersion)
		{
			$PluginVersion = false;
		}
		
		try
		{
			$IconHyperlink = $Client->api('repo')->contents()->show($AuthorName, $Data['name'], 'Plugin Repository/favicon.png')['download_url'];
		}
		catch (Exception $NoVersion)
		{
			$IconHyperlink = 'http://identicon.org/?t=' . $Data['name'];
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
				
				$Templater->Append('Author: ' . $AuthorDisplayName);
				
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
				$Parser = new Parsedown();
				$Templater->Append($Parser->text(base64_decode($Client->api('repo')->contents()->readme($AuthorName, $Data['name'])['content'])));
			$Templater->EndLastTag();
		$Templater->EndLastTag();
	}
}
?>