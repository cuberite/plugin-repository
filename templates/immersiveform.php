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
	
	static function AddImmersiveConfirmationDialog($Message, $Explanation, $RejectRedirectLocation = '/', $Templater)
	{		
		ImmersiveFormTemplate::BeginImmersiveForm($Templater);
			$Templater->BeginTag('h1');
				$Templater->Append($Message);
			$Templater->EndLastTag();
			$Templater->BeginTag('p');
				$Templater->Append($Explanation);
			$Templater->EndLastTag();

			$Templater->BeginTag('form', array('action' => $_SERVER['PHP_SELF'], 'method' => 'POST'));
				$Templater->BeginTag('br', array(), true);
				$Templater->BeginTag('br', array(), true);
				
				$Templater->BeginTag('div', array('class' => 'submit'));
					$Templater->BeginTag('input', array('type' => 'Submit', 'name' => 'DeleteConfirmed', 'value' => 'Yes'), true);
					$Templater->BeginTag('input', array('onclick' => 'window.location.href=\'' . $RejectRedirectLocation . '\'', 'type' => 'Button', 'value' => 'No'), true);
				$Templater->EndLastTag();
			$Templater->EndLastTag();
		ImmersiveFormTemplate::EndImmersiveForm($Templater);
	}
	
	static function AddLiveGravatar($Templater)
	{
		$Templater->BeginTag('script', array('type' => 'application/javascript', 'src' => 'md5.js'));
		$Templater->EndLastTag();
		$Templater->BeginTag('script', array('type' => 'application/javascript'));
			$Templater->Append('function UpdateImage(InputBoxElement) {  document.getElementById("profileimage").src = "http://www.gravatar.com/avatar/" + MD5(InputBoxElement.value) + "?s=140&" + (InputBoxElement.value ? "d=retro" : "d=mm"); }');
		$Templater->EndLastTag();		
	}

	static function AddImmersiveLoginForm($Templater)
	{
		ImmersiveFormTemplate::BeginImmersiveForm($Templater);
			$Templater->BeginTag('h1');
				$Templater->Append('Log in to your account');
			$Templater->EndLastTag();
			$Templater->BeginTag('p');
				$Templater->Append('Not so fast! Use the e-mail address and password that your registered with to sign in.');
				$Templater->BeginTag('br', array(), true);
				$Templater->BeginTag('a', array('href' => $_SERVER['PHP_SELF'] . '?register=1', 'style' => 'text-decoration: none'));
					$Templater->Append('Haven\'t registered yet?');
				$Templater->EndLastTag();
			$Templater->EndLastTag();

			$Templater->BeginTag('form', array('action' => $_SERVER['PHP_SELF'], 'method' => 'POST'));
				$Templater->BeginTag('img', array('src' => 'http://www.gravatar.com/avatar/00000000000000000000000000000000?d=mm&f=y&s=140', 'class' => 'profileimage', 'id' => 'profileimage'), true);
				ImmersiveFormTemplate::AddLiveGravatar($Templater);
				$Templater->BeginTag('div', array('class' => 'input'));
					$Templater->BeginTag('label');
						$Templater->Append('Email:');
					$Templater->EndLastTag();
					$Templater->BeginTag('input', array('autofocus' => 'autofocus', 'required' => 'required', 'type' => 'email', 'name' => 'Username', 'oninput' => 'UpdateImage(this);'), true);
					$Templater->BeginTag('br', array(), true);
					$Templater->BeginTag('label');
						$Templater->Append('Password:');
					$Templater->EndLastTag();
					$Templater->BeginTag('input', array('required' => 'required', 'type' => 'password', 'name' => 'Password'), true);
				$Templater->EndLastTag();

				$Templater->BeginTag('br', array(), true);
				$Templater->BeginTag('br', array(), true);
				
				$Templater->BeginTag('div', array('class' => 'submit'));
					$Templater->BeginTag('input', array('type' => 'Submit', 'name' => 'Login', 'value' => 'Login'), true);
					$Templater->BeginTag('input', array('onclick' => 'window.location.href=\'/\'', 'type' => 'Button', 'value' => 'Cancel'), true);
				$Templater->EndLastTag();
			$Templater->EndLastTag();
		ImmersiveFormTemplate::EndImmersiveForm($Templater);
	}

	static function AddImmersiveRegistrationForm($Templater)
	{
		ImmersiveFormTemplate::BeginImmersiveForm($Templater);
			$Templater->BeginTag('h1');
				$Templater->Append('Create an account');
			$Templater->EndLastTag();
			$Templater->BeginTag('p');
				$Templater->Append('Your profile details will be automatically populated should you have an account with Gravatar.com');
			$Templater->EndLastTag();

			$Templater->BeginTag('form', array('action' => $_SERVER['PHP_SELF'], 'method' => 'POST'));
				$Templater->BeginTag('img', array('src' => 'http://www.gravatar.com/avatar/00000000000000000000000000000000?d=mm&f=y&s=140', 'class' => 'profileimage', 'id' => 'profileimage'), true);
				ImmersiveFormTemplate::AddLiveGravatar($Templater);
				$Templater->BeginTag('div', array('class' => 'input'));
					$Templater->BeginTag('label');
						$Templater->Append('Email:');
					$Templater->EndLastTag();
					$Templater->BeginTag('input', array('autofocus' => 'autofocus', 'required' => 'required', 'type' => 'email', 'name' => 'Username', 'oninput' => 'UpdateImage(this);'), true);
					$Templater->BeginTag('br', array(), true);
					$Templater->BeginTag('label');
						$Templater->Append('Password:');
					$Templater->EndLastTag();
					$Templater->BeginTag('input', array('required' => 'required', 'type' => 'password', 'autocomplete' => 'off', 'name' => 'Password'), true);
				$Templater->EndLastTag();

				$Templater->BeginTag('br', array(), true);
				$Templater->BeginTag('br', array(), true);
				
				$Templater->BeginTag('div', array('class' => 'submit'));
					$Templater->BeginTag('input', array('type' => 'Submit', 'name' => 'Register', 'value' => 'Register'), true);
					$Templater->BeginTag('input', array('onclick' => 'window.location.href=\'/\'', 'type' => 'Button', 'value' => 'Cancel'), true);
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