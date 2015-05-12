<?php
session_start();

require_once 'functions.php';
require_once 'helpers/accountshelper.php';
require_once 'helpers/templater.php';
require_once 'templates/manageaccount.php';

$Template = new Templater();
$SQLLink = new mysqli(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (!AccountsHelper::GetLoggedInDetails($Details))
{
	$Template->SetRedirect('login.php?login=1');
	return;
}

if (isset($_POST['Delete']) && $_POST['Delete'])
{
	list($Username) = $Details;
	$Response = $SQLLink->query("DELETE FROM Accounts WHERE Username = '$Username'");
	
	if ($Response)
	{
		session_destroy();
		$Template->SetRedirect('index.php');
		return;
	}
}

ManageAccountTemplate::AddManagePanel($Template, $Details);
?>