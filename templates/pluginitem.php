<?php

require_once 'helpers/accountshelper.php';
require_once 'helpers/imagehelper.php';

class PluginItemTemplate
{
	static function AddCondensedPluginItem($DominantRGB, $TextRGB, $MinimumAnimationDuration, $SQLEntry, $Templater)
	{
		$Templater->BeginTag('a', array('style' => 'text-decoration: none', 'href' => 'showplugin.php?id=' . $SQLEntry['UniqueID']));
			$Templater->BeginTag('div', array('class' => 'boundedbox plugin showcondensed', 'style' => 'background-color:' . $DominantRGB . '; color:' . $TextRGB . '; animation-duration:' . $MinimumAnimationDuration . 'ms;'));
				$Templater->BeginTag('img', array('class' => 'boundedbox condensedicon show', 'src' => $SQLEntry['Icon']), true);
				$Templater->BeginTag('figcaption', array('class' => 'boundedbox condensedicon caption'));
					$Templater->BeginTag('b');
						$Templater->Append($SQLEntry['PluginName']);
					$Templater->EndLastTag();
					$Templater->BeginTag('br', array(), true);
					$Templater->BeginTag('div', array('class' => 'boundedbox condensedicon caption description'));
						$Templater->Append('Author: ' . $SQLEntry['AuthorDisplayName']);
						$Templater->BeginTag('br', array(), true);
						$Templater->Append('Version: ' . $SQLEntry['PluginVersion']);
					$Templater->EndLastTag();
				$Templater->EndLastTag();
			$Templater->EndLastTag();
		$Templater->EndLastTag();
	}

	static function AddExpandedPluginItem($SQLEntry, $Templater)
	{
		$Templater->BeginTag('article', array('class' => 'boundedbox plugin show infobox'));
			$Templater->BeginTag('nav');
				$Templater->BeginTag('a', array('href' => $SQLEntry['PluginFile']));
					$Templater->BeginTag('img', array('src' => 'images/download.svg', 'class' => 'download', 'alt' => 'Download button', 'title' => 'Download'), true);
				$Templater->EndLastTag();
				if (AccountsHelper::GetLoggedInUsername($Username))
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
					
					if ($Username == $SQLEntry['Author'])
					{
						$Templater->BeginTag('a', array('href' => 'edit.php?id=' . $SQLEntry['UniqueID']));
							$Templater->BeginTag('img', array('src' => 'images/edit.svg', 'class' => 'edit', 'alt' => 'Edit button', 'title' => 'Edit'), true);
						$Templater->EndLastTag();
					}
				}
			$Templater->EndLastTag();

			$Templater->BeginTag('img', array('class' => 'boundedbox expandedicon show', 'src' => $SQLEntry['Icon']), true);
			$Templater->BeginTag('figcaption', array('class' => 'boundedbox expandedicon caption'));
				$Templater->BeginTag('h2');
					$Templater->Append($SQLEntry['PluginName']);
				$Templater->EndLastTag();
				$Templater->Append('Author: ' . $SQLEntry['AuthorDisplayName']);
				$Templater->BeginTag('br', array(), true);
				$Templater->Append('Version: ' . $SQLEntry['PluginVersion']);
				$Templater->BeginTag('br', array(), true);
				$Templater->Append('Rating: ' . $SQLEntry['PluginVersion']);
				$Templater->BeginTag('br', array(), true);
				$Templater->Append('Downloads: ' . $SQLEntry['PluginVersion']);
				$Templater->BeginTag('br', array(), true);
				$Templater->Append('Category: ' . $SQLEntry['PluginVersion']);
			$Templater->EndLastTag();
			$Templater->BeginTag('hr', array(), true);

			$ImageFound = ImageHelper::DisplaySerialisedImages($SQLEntry['Images'], $Templater);
			if ($ImageFound)
			{
				$Templater->BeginTag('hr', array(), true);
			}
			$Templater->BeginTag('p');
				$Templater->Append($SQLEntry['PluginDescription']);
			$Templater->EndLastTag();
		$Templater->EndLastTag();
	}
}
?>