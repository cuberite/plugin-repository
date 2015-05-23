<?php
session_start();

require_once 'functions.php';
require_once 'helpers/templater.php';
require_once 'helpers/meekrodb.php';
require_once 'templates/immersiveform.php';
require_once 'helpers/accountshelper.php';

$Template = new Templater();
$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

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
	if (empty($_POST['Username']) || empty($_POST['Password']))
	{			
		ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'The username or password cannot be empty', $Template);
		$Template->SetRefresh($_SERVER['PHP_SELF']. '?register=1');
		return;
	}
	
	if ($SQLLink->queryFirstRow('SELECT * FROM Accounts WHERE Username = %s', $_POST['Username']) !== null)
	{
		ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'This account already exists', $Template);
		$Template->SetRefresh($_SERVER['PHP_SELF']. '?register=1');
		return;
	}		
	
	$HashedPassword = password_hash($_POST['Password'], PASSWORD_DEFAULT, array('cost' => 12));
	$SQLLink->insert('Accounts', array(
		'Username' => $_POST['Username'],
		'Password' => $HashedPassword
		)
	);
	ImmersiveFormTemplate::AddImmersiveDialog('Operation successful', IMMERSIVE_INFO, 'You\'ve successfully registered and can now log in', $Template);
	$Template->SetRefresh($_SERVER['PHP_SELF'] . '?login=1');
	return;
}
else if (isset($_POST['Login']))
{
	if (password_verify($_POST['Password'], $SQLLink->queryFirstRow('SELECT Password FROM Accounts WHERE Username = %s', $_POST['Username'])['Password']))
	{
		$_SESSION['Username'] = $_POST['Username'];		
		$AccountsHelper = new AccountsHelper;
		list($ProfileImageURL, $FullName, $DisplayName) = $AccountsHelper->GetDetailsFromUsername($_POST['Username']);
		
		$_SESSION['ProfileImageURL'] = $ProfileImageURL;
		$_SESSION['FullName'] = $FullName;
		$_SESSION['DisplayName'] = $DisplayName;
	}
	else
	{
		ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'The username or password was incorrect', $Template);
		$Template->SetRefresh($_SERVER['PHP_SELF'] . '?login=1');
		return;
	}
}

$Template->SetRedirect();
return;

?>