<?php
class ManageAccountTemplate
{
	static function AddManagePanel($Templater, $Details)
	{
		list($Username, $ProfileImageURL, $FullName, $DisplayName) = $Details;
		
		$Templater->BeginTag('article', array('class' => 'boundedbox plugin show infobox'));
			$Templater->BeginTag('img', array('class' => 'boundedbox expandedicon show', 'style' => '', 'src' => $ProfileImageURL), true);
			$Templater->BeginTag('figcaption', array('class' => 'boundedbox expandedicon caption'));
				$Templater->BeginTag('h2');
					$Templater->Append($FullName);
				$Templater->EndLastTag();
				$Templater->Append('Username: ' . $Username);
				$Templater->BeginTag('br', array(), true);
				$Templater->Append('Display name: ' . $DisplayName);
			$Templater->EndLastTag();
		
			$Templater->BeginTag('p');
				$Templater->Append('All personal details are changeable on Gravatar. Additionally, all your base are belong to us.');
			$Templater->EndLastTag();
			
			$Templater->BeginTag('p');
				$Templater->Append('Yep, that\'s in the license.');
			$Templater->EndLastTag();
		$Templater->EndLastTag();

		$Templater->BeginTag('article', array('class' => 'boundedbox plugin add'));
			$Templater->BeginTag('form', array('action' => $_SERVER['PHP_SELF'], 'method' => 'POST'));
				$Templater->BeginTag('input', array('style' => 'height: 50px; margin-left: auto; margin-right: auto; display: block;', 'name' => 'Delete', 'type' => 'Submit', 'value' => 'Close account'));
			$Templater->EndLastTag();
		$Templater->EndLastTag();
	}
}
?>