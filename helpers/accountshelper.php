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
			$DisplayName = empty($Profile['entry'][0]['displayName']) ? $Username : $Profile['entry'][0]['displayName'];
			$FullName = empty($Profile['entry'][0]['name']['formatted']) ? $DisplayName : $Profile['entry'][0]['name']['formatted'];
			$Details = array($Profile['entry'][0]['thumbnailUrl'], $FullName, $DisplayName);
		}
		else
		{
			$Details = array('http://www.gravatar.com/avatar/' . $Hashername . '?d=retro', $Username, $Username);
		}
		
		return $Details;
	}
}
?>