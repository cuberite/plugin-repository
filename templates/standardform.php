<?php
require_once 'helpers/githubapihelper.php';

class StandardFormTemplate
{
	static private function BeginStandardForm($Templater)
	{
		$Templater->BeginTag('div', array('class' => 'boundedbox expandedplugin'));
	}

	static function AddCreatePluginForm($Templater)
	{
		StandardFormTemplate::BeginStandardForm($Templater);
			$Templater->BeginTag('h2');
				$Templater->Append('Add a new entry');
			$Templater->EndLastTag();
			$Templater->BeginTag('hr', array('style' => 'margin-top: -15px'), true);

			$Templater->BeginTag('form', array('action' => $_SERVER['PHP_SELF'], 'method' => 'POST', 'enctype' => 'multipart/form-data'));
				
				foreach (GitHubAPI::GetAllCurrentUserRepositories() as $RepositoryOwner => $Repositories)
				{
					$Templater->BeginTag('h3');
						$Templater->Append($RepositoryOwner);
					$Templater->EndLastTag();
					
					foreach ($Repositories as $Repository)
					{
						$Templater->BeginTag('input', array('required' => 'required', 'type' => 'radio', 'name' => 'RepositoryID', 'value' => $Repository['id']), true);
						$Templater->BeginTag('label');
							$Templater->Append('<strong>' . $Repository['name'] . '</strong> ');
							
							$Templater->BeginTag('em');
								$Templater->Append($Repository['description']);
							$Templater->EndLastTag();
						$Templater->EndLastTag();
						
						$Templater->BeginTag('br', array(), true);
					}
				}
				
				$Templater->BeginTag('hr', array(), true);
				$Templater->BeginTag('p');
					$Templater->Append('<strong>No organisation?</strong> Ensure that your membership of it is public.');
				$Templater->EndLastTag();
				$Templater->BeginTag('p');
					$Templater->Append('<strong>Favicons:</strong> add a Favicon.png file to the base directory.');
					$Templater->BeginTag('br', array(), true);
					$Templater->Append('<strong>Description:</strong> add a Info.lua file to root, with a Description field.');
				$Templater->EndLastTag();
				
				$Templater->BeginTag('input', array('name' => 'Submit', 'type' => 'Submit', 'value' => 'Submit entry'), true);
			$Templater->EndLastTag();
		StandardFormTemplate::EndStandardForm($Templater);
	}

	static function AddEditPluginForm($SQLEntry, $Templater)
	{
		StandardFormTemplate::BeginStandardForm($Templater);
			$Templater->BeginTag('h2');
				$Templater->Append('Edit an existing entry');
			$Templater->EndLastTag();
			$Templater->BeginTag('hr', array('style' => 'margin-top: -15px'), true);

			$Templater->BeginTag('form', array('action' => $_SERVER['PHP_SELF'] . '?id=' . $SQLEntry['RepositoryID'], 'method' => 'POST'));
				$Templater->BeginTag('input', array('style' => 'height: 50px; margin-left: auto; margin-right: auto; display: block;', 'name' => 'Delete' . $SQLEntry['RepositoryID'], 'type' => 'Submit', 'value' => 'Delete entry'));
			$Templater->EndLastTag();
		StandardFormTemplate::EndStandardForm($Templater);
	}

	static private function EndStandardForm($Templater)
	{
		$Templater->EndLastTag();
	}
}
?>