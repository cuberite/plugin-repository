<?php
session_start();

require_once 'functions.php';
require_once 'helpers/accountshelper.php';
require_once 'helpers/templater.php';
require_once 'templates/manageaccount.php';
require_once 'templates/immersiveform.php';

$Template = new Templater();
$SQLLink = new mysqli(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (!AccountsHelper::GetLoggedInDetails($Details))
{
	$Template->SetRedirect('login.php?login=1');
	return;
}

if (isset($_POST['DeleteConfirmed']) && $_POST['DeleteConfirmed'])
{
	list($Username) = $Details;
	$Response = $SQLLink->query("DELETE FROM Accounts WHERE Username = '$Username'");
	
	if ($Response)
	{
		session_destroy();
		$Template->SetRedirect();
	}
	else
	{
		ImmersiveFormTemplate::AddImmersiveDialog('An error occurred', IMMERSIVE_ERROR, 'MySQL errored: ' . $SQLLink->error, $Template);
		$Template->SetRefresh();
	}
	return;
}

if (isset($_POST['Delete']) && $_POST['Delete'])
{
	ImmersiveFormTemplate::AddImmersiveConfirmationDialog('Account deletion confirmation', 'Deleting your account will allow others to register with the same name and take over your plugins. Are you sure?', $_SERVER['PHP_SELF'], $Template);
	return;
}

ManageAccountTemplate::AddManagePanel($Template, $Details);
?>