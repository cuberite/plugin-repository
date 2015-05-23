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
	
	private $CURLInstance;	
	function __construct()
	{
		$this->CURLInstance = curl_init();
		curl_setopt($this->CURLInstance, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->CURLInstance, CURLOPT_FOLLOWLOCATION, true);
	}	
	
	function GetDetailsFromUsername($Username)
	{
		$Details;
		$Hashername = hash('md5', strtolower(trim($Username)));
		curl_setopt($this->CURLInstance, CURLOPT_URL, 'https://gravatar.com/' . $Hashername . '.php');
		$Profile = unserialize(curl_exec($this->CURLInstance));
		
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