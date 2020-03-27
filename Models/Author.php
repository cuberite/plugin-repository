<?php
final class AuthorGenerator
{
	public static function GenerateAndUpdate($User)
	{
		$AuthorDetails = array(
			'AuthorId' => $User['id'],
			'Login' => $User['login'],
			'DisplayName' => $User['name'],
			'AvatarHyperlink' => $User['avatar_url']
		);

		DB::insertUpdate('Authors', $AuthorDetails);
		return $AuthorDetails;
	}
}

final class Author
{
	public function __construct($Id, $Login, $Name, $Avatar)
	{
		$this->AuthorId = $Id;
		$this->Login = $Login;
		$this->DisplayName = $Name;
		$this->AvatarHyperlink = $Avatar;
	}

	public $AuthorId;
	public $Login;
	public $DisplayName;
	public $AvatarHyperlink;
}
?>