<?php
class ImageHelper
{
	static function GetDominantColorAndTextColour($ImageLocation, &$DominantRGB, &$TextRGB)
	{
		$Image = imagecreatefromstring($ImageLocation);
		$ImageWidth = imagesx($Image);
		$ImageHeight = imagesy($Image);
		$RGBCounter = array();

		for ($X = 0; $X < $ImageWidth; $X += $ImageWidth / 10)
		{
			for ($Y = 0; $Y < $ImageHeight; $Y += $ImageHeight / 10)
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

		$DominantColour = array_search(max($RGBCounter), $RGBCounter);

		if (
			(
				0.21 * (($DominantColour & 0xFF0000) >> 16) +
				0.72 * (($DominantColour & 0x00FF00) >> 8) +
				0.07 * ($DominantColour & 0x0000FF)
			) > 127.5
		)
		{
			$TextRGB = '#000000';
		}
		else
		{
			$TextRGB = '#FFFFFF';
		}

		$DominantRGB = '#' . str_pad(dechex($DominantColour), 6, '0', STR_PAD_LEFT);
	}
}
?>