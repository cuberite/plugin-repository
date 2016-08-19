<?php

require_once 'helpers/accountshelper.php';
require_once 'helpers/imagehelper.php';
require_once 'helpers/githubapihelper.php';
require_once '../composer/vendor/autoload.php';

class PluginItemTemplate
{
	static function AddCondensedPluginItem($MinimumAnimationDuration, $SQLEntry, $Templater)
	{
		list($RepositoryName, , , $RepositoryVersion, $Description, $License) = GitHubAPI::GetCachedRepositoryMetadata($SQLEntry['RepositoryID']);
		list($IconHyperlink, $DominantRGB, $TextRGB) = GitHubAPI::GetCachedRepositoryIconData($SQLEntry['RepositoryID']);
		list(, $AuthorDisplayName) = AccountsHelper::GetDetailsFromID($SQLEntry['AuthorID']);

		$Templater->BeginTag('a', array('style' => 'text-decoration: none', 'href' => 'showplugin.php?id=' . $SQLEntry['RepositoryID']));
			$Templater->BeginTag('figure', array('class' => 'boundedbox condensedplugin', 'style' => 'background-color:' . $DominantRGB . '; color:' . $TextRGB . '; animation-duration:' . $MinimumAnimationDuration . 'ms;'));

				$Templater->BeginTag('img', array('src' => $IconHyperlink, 'alt' => $RepositoryName), true);
				
				$Templater->BeginTag('figcaption');
				
					$Templater->BeginTag('strong');
						$Templater->Append($RepositoryName);
					$Templater->EndLastTag();
					
					$Templater->BeginTag('br', array(), true);
					
					$Templater->Append('Author: ' . $AuthorDisplayName);
					
					if ($RepositoryVersion)
					{
						$Templater->BeginTag('br', array(), true);
						$Templater->Append('Version: ' . $RepositoryVersion);
					}
					
					if ($License)
					{
						$Templater->BeginTag('br', array(), true);
						$Templater->Append('License: ' . $License);
					}
					
					if ($Description)
					{
						$Templater->BeginTag('p');
							$Templater->Append($Description);
						$Templater->EndLastTag();
					}
				$Templater->EndLastTag();

			$Templater->EndLastTag();
		$Templater->EndLastTag();
	}

	static function AddExpandedPluginItem($SQLEntry, $Templater)
	{
		list($RepositoryName, $RepositoryFullName, $RepositoryOwnerName, $RepositoryVersion, , $License) = GitHubAPI::GetCachedRepositoryMetadata($SQLEntry['RepositoryID']);
		list($IconHyperlink, $DominantRGB, $TextRGB) = GitHubAPI::GetCachedRepositoryIconData($SQLEntry['RepositoryID']);
		list(, $AuthorDisplayName) = AccountsHelper::GetDetailsFromID($SQLEntry['AuthorID']);

		$Templater->BeginTag('article', array('class' => 'boundedbox expandedplugin'));
			$Templater->BeginTag('header', array('style' => 'background-color:' . $DominantRGB . '; outline-color:' . $DominantRGB . '; color:' . $TextRGB));
				$Templater->BeginTag('figure');
					$Templater->BeginTag('img', array('src' => $IconHyperlink, 'alt' => $RepositoryName), true);
					$Templater->BeginTag('figcaption');
					
						$Templater->BeginTag('h2');
							$Templater->Append($RepositoryName);
						$Templater->EndLastTag();

						$Templater->Append('Author: ' . $AuthorDisplayName);
						$Templater->BeginTag('br', array(), true);
						$Templater->Append('Owned by: ' . $RepositoryOwnerName);
						
						if ($License)
						{
							$Templater->BeginTag('br', array(), true);
							$Templater->Append('License: ' . $License);
						}

						if ($RepositoryVersion)
						{
							$Templater->BeginTag('br', array(), true);
							$Templater->Append('Version: ' . $RepositoryVersion);
							$Templater->BeginTag('br', array(), true);
							$Templater->Append('Rating: ' . $RepositoryVersion);
							$Templater->BeginTag('br', array(), true);
							$Templater->Append('Downloads: ' . $RepositoryVersion);
							$Templater->BeginTag('br', array(), true);
							$Templater->Append('Category: ' . $RepositoryVersion);
						}
					$Templater->EndLastTag();
				$Templater->EndLastTag();
				
				$Templater->BeginTag('nav');
				
					$Templater->BeginTag('figure');
						$Templater->BeginTag('figcaption');
							$Templater->Append('✓ ');
						$Templater->EndLastTag();
						
						$Templater->BeginTag('a', array('href' => 'https://api.github.com/repos/' . $RepositoryFullName . '/zipball'));							
							$Templater->BeginTag('img', array('src' => 'images/download.svg', 'class' => 'download', 'alt' => 'Download latest release button', 'title' => 'Download latest release'), true);
						$Templater->EndLastTag();
					$Templater->EndLastTag();
					
					$Templater->BeginTag('div');
					$Templater->EndLastTag();
					
					$Templater->BeginTag('figure');
						$Templater->BeginTag('figcaption');
							$Templater->Append('🐜 ');
						$Templater->EndLastTag();
						
						$Templater->BeginTag('a', array('href' => 'https://api.github.com/repos/' . $RepositoryFullName . '/zipball'));							
							$Templater->BeginTag('img', array('src' => 'images/download.svg', 'class' => 'download', 'alt' => 'Download latest commit button', 'title' => 'Download latest commit'), true);
						$Templater->EndLastTag();
					$Templater->EndLastTag();
					
					$Templater->BeginTag('div');
					$Templater->EndLastTag();
					
					$Templater->BeginTag('hr', array(), true);
					
					$Templater->BeginTag('figure');
						$Templater->BeginTag('a', array('href' => 'https://github.com/' . $RepositoryFullName));
							$Templater->BeginTag('img', array('src' => 'images/github.svg', 'class' => 'github', 'alt' => 'Link to GitHub repository button', 'title' => 'Go to repository on GitHub'), true);
						$Templater->EndLastTag();
					$Templater->EndLastTag();
					
					if (AccountsHelper::GetLoggedInDetails($Details))
					{
						$Templater->BeginTag('figure');
							$Templater->BeginTag('a', array('href' => '#comments'));
								$Templater->BeginTag('img', array('src' => 'images/review.svg', 'class' => 'review', 'alt' => 'Review button', 'title' => 'Review'), true);
							$Templater->EndLastTag();
						$Templater->EndLastTag();
						
						$Templater->BeginTag('figure');
							$Templater->BeginTag('a', array('href' => '#comments'));
								$Templater->BeginTag('img', array('src' => 'images/rate.svg', 'class' => 'rate', 'alt' => 'Rate button', 'title' => 'Rate'), true);
							$Templater->EndLastTag();
						$Templater->EndLastTag();
						
						$Templater->BeginTag('figure');
							$Templater->BeginTag('a', array('href' => '#comments'));
								$Templater->BeginTag('img', array('src' => 'images/favourite.svg', 'class' => 'favourite', 'alt' => 'Favourite button', 'title' => 'Favourite'), true);
							$Templater->EndLastTag();
						$Templater->EndLastTag();
						
						$Templater->BeginTag('figure');
							$Templater->BeginTag('a', array('href' => '#comments'));
								$Templater->BeginTag('img', array('src' => 'images/report.svg', 'class' => 'report', 'alt' => 'Report button', 'title' => 'Report'), true);
							$Templater->EndLastTag();
						$Templater->EndLastTag();

						if ($Details[0] == $SQLEntry['AuthorID'])
						{
							$Templater->BeginTag('figure');
								$Templater->BeginTag('a', array('href' => 'edit.php?id=' . $SQLEntry['RepositoryID']));
									$Templater->BeginTag('img', array('src' => 'images/edit.svg', 'class' => 'edit', 'alt' => 'Edit button', 'title' => 'Edit'), true);
								$Templater->EndLastTag();
							$Templater->EndLastTag();
						}
					}
				$Templater->EndLastTag();
			$Templater->EndLastTag();

			$ImageFound = false;//ImageHelper::DisplaySerialisedImages($SQLEntry['Images'], $Templater);
			if ($ImageFound)
			{
				$Templater->BeginTag('hr', array(), true);
			}
			
			$Templater->Append((new Parsedown())->text(GitHubAPI::GetCachedRepositoryReadme($SQLEntry['RepositoryID'])));
		$Templater->EndLastTag();
	}
}
?>
