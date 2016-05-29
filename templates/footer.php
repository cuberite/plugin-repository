<?php

class FooterTemplate
{
	static function AddFooter($Templater)
	{
		$Templater->BeginTag('footer', array('id' => 'footer'));
			$Templater->BeginTag('ul');
				$Templater->BeginTag('li');
					$Templater->BeginTag('a', array('href' => 'https://github.com/cuberite/plugin-repository'));
						$Templater->Append('Cuberite Plugin Repository is on GitHub');
					$Templater->EndLastTag();
				$Templater->EndLastTag();

				$Templater->BeginTag('li');
					$Templater->BeginTag('a', array('href' => '/copyright.php'));
						$Templater->Append('Copyright');
					$Templater->EndLastTag();
				$Templater->EndLastTag();
			$Templater->EndLastTag();

		$Templater->EndLastTag();
	}
}

?>
