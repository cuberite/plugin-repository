<?php
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