<?php
class StandardFormTemplate
{
	static private function BeginStandardForm($Templater)
	{
		$Templater->BeginTag('div', array('class' => 'boundedbox plugin add'));
	}

	static function AddCreatePluginForm($Templater)
	{
		StandardFormTemplate::BeginStandardForm($Templater);
			$Templater->BeginTag('h2');
				$Templater->Append('Add a new entry');
			$Templater->EndLastTag();
			$Templater->BeginTag('hr', array('style' => 'margin-top: -15px'), true);

			$Templater->BeginTag('form', array('action' => $_SERVER['PHP_SELF'], 'method' => 'POST', 'enctype' => 'multipart/form-data'));
				$Templater->BeginTag('label');
					$Templater->Append('Name:');
				$Templater->EndLastTag();
				$Templater->BeginTag('input', array('required' => 'required', 'type' => 'text', 'name' => 'PluginName'), true);

				$Templater->BeginTag('label');
					$Templater->Append('Version:');
				$Templater->EndLastTag();
				$Templater->BeginTag('input', array('required' => 'required', 'type' => 'text', 'name' => 'PluginVersion'), true);

				$Templater->BeginTag('label');
					$Templater->Append('Icon:');
				$Templater->EndLastTag();
				$Templater->BeginTag('input', array('name' => 'icon', 'type' => 'file', 'size' => '6'), true);

				$Templater->BeginTag('label');
					$Templater->Append('Images:');
				$Templater->EndLastTag();
				$Templater->BeginTag('input', array('multiple' => 'multiple', 'name' => 'images[]', 'type' => 'file', 'size' => '6'), true);

				$Templater->BeginTag('label');
					$Templater->Append('The Actual Download&trade;:');
				$Templater->EndLastTag();
				$Templater->BeginTag('input', array('name' => 'pluginfile', 'required' => 'required', 'type' => 'file', 'size' => '6'), true);

				$Templater->BeginTag('label');
					$Templater->Append('Description:');
				$Templater->EndLastTag();
				$Templater->BeginTag('textarea', array('required' => 'required', 'name' => 'PluginDescription', 'id' => 'ckeditor', 'rows' => 10, 'cols' => 67));
				$Templater->EndLastTag();
				$Templater->BeginTag('br', array(), true);

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

			$Templater->BeginTag('form', array('action' => $_SERVER['PHP_SELF'] . '?id=' . $SQLEntry['UniqueID'], 'method' => 'POST', 'enctype' => 'multipart/form-data'));
				$Templater->BeginTag('label');
					$Templater->Append('Name:');
				$Templater->EndLastTag();
				$Templater->BeginTag('input', array('required' => 'required', 'type' => 'text', 'name' => 'PluginName', 'value' => $SQLEntry['PluginName']), true);

				$Templater->BeginTag('label');
					$Templater->Append('Version:');
				$Templater->EndLastTag();
				$Templater->BeginTag('input', array('required' => 'required', 'type' => 'text', 'name' => 'PluginVersion', 'value' => $SQLEntry['PluginVersion']), true);

				$Templater->BeginTag('label');
					$Templater->Append('Icon:');
				$Templater->EndLastTag();
				$Templater->BeginTag('input', array('name' => 'icon', 'type' => 'file', 'size' => '6'), true);

				$Templater->BeginTag('label');
					$Templater->Append('Images:');
				$Templater->EndLastTag();
				$Templater->BeginTag('input', array('multiple' => 'multiple', 'name' => 'images[]', 'type' => 'file', 'size' => '6'), true);

				$Templater->BeginTag('label');
					$Templater->Append('The Actual Download&trade;:');
				$Templater->EndLastTag();
				$Templater->BeginTag('input', array('name' => 'pluginfile', 'type' => 'file', 'size' => '6'), true);

				$Templater->BeginTag('label');
					$Templater->Append('Description:');
				$Templater->EndLastTag();
				$Templater->BeginTag('textarea', array('required' => 'required', 'name' => 'PluginDescription', 'id' => 'ckeditor', 'rows' => 10, 'cols' => 67));
					$Templater->Append(str_replace('&lt;', '&amp;lt;', str_replace('&gt;', '&amp;gt;', $SQLEntry['PluginDescription'])));
				$Templater->EndLastTag();
				$Templater->BeginTag('br', array(), true);

				$Templater->BeginTag('input', array('name' => 'Edit' . $SQLEntry['UniqueID'], 'type' => 'Submit', 'value' => 'Edit entry'), true);
				$Templater->BeginTag('input', array('name' => 'Delete' . $SQLEntry['UniqueID'], 'type' => 'Submit', 'value' => 'Delete entry'), true);
			$Templater->EndLastTag();
		StandardFormTemplate::EndStandardForm($Templater);
	}

	static private function EndStandardForm($Templater)
	{
		$Templater->EndLastTag();
	}
}
?>