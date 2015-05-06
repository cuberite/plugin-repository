<?php
class ImageHelper
{
	static function DisplaySerialisedImages($ImageArray, $Templater)
	{
		$ImageFound = false;
		$ImageArray = unserialize($ImageArray);

		if (!empty($ImageArray))
		{
			$Templater->BeginTag('div', array('style' => 'text-align: center;'));
				$Templater->BeginTag('div', array('class' => 'bss-slides num1', 'tabindex' => '1', 'autofocus' => 'autofocus'));
					foreach ($ImageArray as $ImageValue)
					{
						if (!empty($ImageValue))
						{
							$ImageFound = true;
							$Templater->BeginTag('figure');
								$Templater->BeginTag('img', array('class' => 'boundedbox screenshot', 'src' => $ImageValue), true);
							$Templater->EndLastTag();
						}
					}
				$Templater->EndLastTag();
			$Templater->EndLastTag();
		}

		return $ImageFound;
	}
	
	static function GetDominantColorAndTextColour($ImageLocation, &$DominantRGB, &$TextColour)
	{
		$Image;
		switch (strtolower(pathinfo($ImageLocation, PATHINFO_EXTENSION)))
		{
			case 'jpeg':
			case 'jpg': $Image = imagecreatefromjpeg($ImageLocation); break;
			case 'png': $Image = imagecreatefrompng($ImageLocation); break;
			case 'gif': $Image = imagecreatefromgif($ImageLocation); break;

			default:
			{
				$DominantRGB = '#FFFFFF';
				$TextColour = '#000000';
				return;
			}
		}

		$ImageWidth = imagesx($Image);
		$ImageHeight = imagesy($Image);
		$RGBCounter = array();

		for ($X = 0; $X < $ImageWidth; $X += $ImageWidth / 1)
		{
			for ($Y = 0; $Y < $ImageHeight; $Y += $ImageHeight / 1)
			{
				$Color = ImageColorAt($Image, $X, $Y);
				if (isset($RGBCounter[$Color]))
				{
					$RGBCounter[$Color]++;
				}
				else
				{
					$RGBCounter[$Color] = 0;
				}
			}
		}

		$DominantRGB = '#' . dechex(array_search(max($RGBCounter), $RGBCounter));
		$DominantRGB = str_pad($DominantRGB, 7, '0', STR_PAD_RIGHT);
		if (hexdec(substr($DominantRGB, 0, 2)) + hexdec(substr($DominantRGB, 2, 4)) + hexdec(substr($DominantRGB, 4, 6)) > 100000)
		{
			$TextColour = '#000000';
		}
		else
		{
			$TextColour = '#FFFFFF';
		}
	}
}
?>