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
	
	static function GetDetailsFromUsername($Username)
	{
		$Details;
		$Hashername = hash('md5', strtolower(trim($Username)));
		$Profile = unserialize(@file_get_contents('https://gravatar.com/' . $Hashername . '.php'));
		
		if (is_array($Profile) && isset($Profile['entry']))
		{
			$Details = array($Profile['entry'][0]['thumbnailUrl'], $Profile['entry'][0]['name']['formatted'], $Profile['entry'][0]['displayName']);
		}
		else
		{
			$Details = array('http://www.gravatar.com/avatar/' . $Hashername . '?d=retro', $Username, $Username);
		}
		
		return $Details;
	}
}
?>