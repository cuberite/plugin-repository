<?php
class ImmersiveFormTemplate
{
	static private function BeginImmersiveForm($Templater)
	{
		$Templater->BeginTag('div', array('class' => 'errormessage greyout'));
		$Templater->BeginTag('div', array('class' => 'errormessage messagestrip'));
		$Templater->BeginTag('div', array('class' => 'errormessage message form'));
	}
	
	static function AddImmersiveDialog($Message, $Type, $Explanation, $Templater)
	{
		switch ($Type)
		{
			case IMMERSIVE_INFO: $Subclass = 'info'; break;
			case IMMERSIVE_ERROR: $Subclass = 'error'; break;
			default: $Subclass = 'form'; break;
		}

		$Templater->BeginTag('div', array('class' => 'errormessage greyout'));
			$Templater->BeginTag('div', array('class' => 'errormessage messagestrip'));
				$Templater->BeginTag('div', array('class' => 'errormessage message ' . $Subclass));
					$Templater->BeginTag('h1');
						$Templater->Append($Message);
					$Templater->EndLastTag();
					$Templater->BeginTag('p');
						$Templater->Append($Explanation);
					$Templater->EndLastTag();
					$Templater->BeginTag('p');
						$Templater->BeginTag('img', array('class' => 'spinner', 'src' => 'images/spinner.gif'), true);
						$Templater->Append('Please wait whilst you are redirected');
					$Templater->EndLastTag();
				$Templater->EndLastTag();
			$Templater->EndLastTag();
		$Templater->EndLastTag();
	}
	
	static function AddImmersiveConfirmationDialog($Message, $Explanation, $ConfirmationButtonName, $AcceptRedirectLocation, $RejectRedirectLocation, $Templater)
	{		
		ImmersiveFormTemplate::BeginImmersiveForm($Templater);
			$Templater->BeginTag('h1');
				$Templater->Append($Message);
			$Templater->EndLastTag();
			$Templater->BeginTag('p');
				$Templater->Append($Explanation);
			$Templater->EndLastTag();

			$Templater->BeginTag('form', array('action' => $AcceptRedirectLocation, 'method' => 'POST'));
				$Templater->BeginTag('br', array(), true);
				$Templater->BeginTag('br', array(), true);
				
				$Templater->BeginTag('div', array('class' => 'submit'));
					$Templater->BeginTag('input', array('type' => 'Submit', 'name' => $ConfirmationButtonName, 'value' => 'Yes'), true);
					$Templater->BeginTag('input', array('onclick' => 'window.location.href=\'' . $RejectRedirectLocation . '\'', 'type' => 'Button', 'value' => 'No'), true);
				$Templater->EndLastTag();
			$Templater->EndLastTag();
		ImmersiveFormTemplate::EndImmersiveForm($Templater);
	}

	static private function EndImmersiveForm($Templater)
	{
		$Templater->EndLastTag(); // </div>
		$Templater->EndLastTag(); // </div>
		$Templater->EndLastTag(); // </div>
	}
}
?>