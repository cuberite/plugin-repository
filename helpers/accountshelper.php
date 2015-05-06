<?php
class AccountsHelper
{
	static function GetLoggedInUsername(&$Username = NULL, &$DisplayName = NULL)
	{
		if (!isset($_SESSION['Username']))
		{
			return false;
		}
		else
		{
			$Username = $_SESSION['Username'];
			$DisplayName = $_SESSION['DisplayName'];
			return true;
		}
	}

	static function GetLoggedInDetails(&$Details)
	{
		if (!AccountsHelper::GetLoggedInUsername($Username))
		{
			return false;
		}

		$Details = array($Username, $_SESSION['ProfileImageURL'], $_SESSION['FullName'], $_SESSION['DisplayName']);
		return true;
	}
}
?>