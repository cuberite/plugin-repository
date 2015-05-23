<?php
session_start();

require_once 'functions.php';
require_once 'helpers/accountshelper.php';
require_once 'helpers/templater.php';
require_once 'helpers/meekrodb.php';
require_once 'templates/manageaccount.php';
require_once 'templates/immersiveform.php';

$Template = new Templater();
$SQLLink = new MeekroDB(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);
$SQLLink->error_handler = false;
$SQLLink->throw_exception_on_error = true; 

if (!AccountsHelper::GetLoggedInDetails($Details))
{
	$Template->SetRedirect('login.php?login=1');
	return;
}

if (isset($_POST['DeleteConfirmed']) && $_POST['DeleteConfirmed'])
{
	list($Username) = $Details;
	
	try
	{
		$Response = $SQLLink->query('DELETE FROM Accounts WHERE Username = %s', $Username);
	}
	catch (MeekroDBException $Exception)
	{
		ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'MySQL errored: ' . $Exception->getMessage(), $Template);
		$Template->SetRefresh();
		return;
	}
	
	session_destroy();
	$Template->SetRedirect();
	return;
}

if (isset($_POST['Delete']) && $_POST['Delete'])
{
	ImmersiveFormTemplate::AddImmersiveConfirmationDialog('Account deletion confirmation', 'Deleting your account will allow others to register with the same name and take over your plugins. Are you sure?', $_SERVER['PHP_SELF'], $Template);
	return;
}

ManageAccountTemplate::AddManagePanel($Template, $Details);
?>