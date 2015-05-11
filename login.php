<?php
session_start();

require_once 'functions.php';
require_once 'helpers/templater.php';
require_once 'templates/immersiveform.php';

$Template = new Templater();
$SQLLink = new mysqli(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (isset ($_GET['logout']) && $_GET['logout'])
{
	session_unset();
	session_destroy();
	header('Location:');
	return;
}
if (isset ($_GET['login']) && $_GET['login'])
{
	ImmersiveFormTemplate::AddImmersiveLoginForm($Template);
	return;
}
if (isset ($_GET['register']) && $_GET['register'])
{
	ImmersiveFormTemplate::AddImmersiveRegistrationForm($Template);
	return;
}

if (isset($_POST["Register"]))
{
	if (
		GetAndVerifyPostData($User, 'Username', $SQLLink) or
		GetAndVerifyPostData($Pass, 'Password', $SQLLink)
		)
	{
		ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'The input was invalid or malformed', $Template);
		$Template->SetRefresh($_SERVER['PHP_SELF']. '?register=1');
		return;
	}
	else
	{
		$HashedPassword = password_hash($Pass, PASSWORD_DEFAULT, array('cost' => 12));
		if ($SQLLink->query("SELECT * FROM Accounts WHERE Username = '$User'")->fetch_array() !== null)
		{
			ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'This account already exists', $Template);
			$Template->SetRefresh($_SERVER['PHP_SELF']. '?register=1');
			return;
		}
		else
		{
			$SQLLink->query(
				"INSERT INTO Accounts (Username, Password)
				VALUES ('$User', '$HashedPassword')"
			);
			ImmersiveFormTemplate::AddImmersiveDialog('Operation successful', IMMERSIVE_INFO, 'You\'ve successfully registered and can now log in', $Template);
			$Template->SetRefresh($_SERVER['PHP_SELF'] . '?login=1');
		}
	}
}
else if (isset($_POST['Login']))
{
	if (
		GetAndVerifyPostData($User, 'Username', $SQLLink) or
		GetAndVerifyPostData($Pass, 'Password', $SQLLink)
		)
	{
		ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'The input was invalid or malformed', $Template);
		$Template->SetRefresh($_SERVER['PHP_SELF'] . '?login=1');
		return;
	}
	else
	{
		if (password_verify($Pass, $SQLLink->query("SELECT Password FROM Accounts WHERE Username = '$User'")->fetch_array()['Password']))
		{
			$Hashername = hash('md5', strtolower(trim($User)));
			$_SESSION['Username'] = $User;

			$Profile = unserialize(@file_get_contents('https://gravatar.com/' . $Hashername . '.php'));
			if (is_array($Profile) && isset($Profile['entry']))
			{
				$_SESSION['ProfileImageURL'] = $Profile['entry'][0]['thumbnailUrl'];
				$_SESSION['FullName'] = $Profile['entry'][0]['name']['formatted'];
				$_SESSION['DisplayName'] = $Profile['entry'][0]['displayName'];
			}
			else
			{
				$_SESSION['ProfileImageURL'] = 'http://www.gravatar.com/avatar/' . $Hashername . '?d=retro';
				$_SESSION['FullName'] = $User;
				$_SESSION['DisplayName'] = $User;
			}
		}
		else
		{
			ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'The username or password was incorrect', $Template);
			$Template->SetRefresh($_SERVER['PHP_SELF'] . '?login=1');
			return;
		}
	}
}

$Template->SetRedirect();
return;

?>