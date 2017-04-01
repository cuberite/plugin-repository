<?php namespace GitHubAPI;
require_once 'Base.php';

final class Users
{
	use GitHubAPIProvider;

	static function GetDetailsFromID($UserID)
	{
		$Profile = Users::CustomRequest('user', $UserID);
		return $Profile;
	}
}
?>