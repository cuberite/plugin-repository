<?php
require_once 'helpers/accountshelper.php';

class HeaderTemplate
{
	static function AddHeader($Templater)
	{
		$Templater->BeginTag('header');
			$Templater->BeginTag('h1');
				$Templater->BeginTag('a', array('href' => '/'));
					$Templater->BeginTag('img', array('src' => 'https://raw.githubusercontent.com/mc-server/MCServer/master/src/Resources/icon_256.png', 'alt' => 'MCServer Logo', 'class' => 'logo'), true);
				$Templater->EndLastTag();
				$Templater->BeginTag('div', array('class' => 'vr'));
				$Templater->EndLastTag();
				$Templater->Append('Plugin Repository');
			$Templater->EndLastTag();

			$Templater->BeginTag('nav');
				$Templater->BeginTag('a', array('href' => '/'));
					$Templater->BeginTag('img', array('src' => 'images/home.svg', 'alt' => 'Home button', 'class' => 'home', 'alt' => 'Home button', 'title' => 'Home'), true);
				$Templater->EndLastTag();
				$Templater->BeginTag('a', array('href' => 'addnew.php'));
					$Templater->BeginTag('img', array('src' => 'images/addnew.svg', 'alt' => 'Add new entry button', 'class' => 'addnew', 'alt' => 'Add new entry button', 'title' => 'Add new entry'), true);
				$Templater->EndLastTag();
			$Templater->EndLastTag();

			$Templater->BeginTag('div', array('class' => 'profileheader'));
				if (AccountsHelper::GetLoggedInDetails($Details))
				{
					list($Username, $ProfileImageURL, $FullName) = $Details;
					$Templater->BeginTag('img', array('src' => $ProfileImageURL, 'class' => 'profileimage set', 'alt' => 'User Gravatar'), true);
					$Templater->BeginTag('div', array('class' => 'profilename set'));
						$Templater->BeginTag('b');
							$Templater->Append($FullName);
						$Templater->EndLastTag();
						$Templater->BeginTag('br', array(), true);
						$Templater->BeginTag('a', array('href' => 'manage.php'));
							$Templater->Append('Manage account');
						$Templater->EndLastTag();
						$Templater->BeginTag('br', array(), true);
						$Templater->BeginTag('a', array('href' => 'login.php?logout=1'));
							$Templater->Append('Logout');
						$Templater->EndLastTag();
					$Templater->EndLastTag();
				}
				else
				{
					$Templater->BeginTag('img', array('src' => 'http://www.gravatar.com/avatar/00000000000000000000000000000000?d=mm&f=y', 'class' => 'profileimage'), true);
					$Templater->BeginTag('div', array('class' => 'profilename'));
						$Templater->BeginTag('a', array('href' => 'login.php?login=1'));
							$Templater->Append('Login');
						$Templater->EndLastTag();
					$Templater->EndLastTag();
				}
			$Templater->EndLastTag();

			$Templater->BeginTag('div', array('class' => 'search'));
				$Templater->BeginTag('form', array('action' => 'search.php', 'method' => 'POST'));
					$Templater->BeginTag('input', array('required' => 'required', 'placeholder' => 'Search by ID, title, or author', 'size' => '25', 'type' => 'text', 'name' => 'Query'), true);
					$Templater->BeginTag('select', array('name' => 'Method'));
						$Templater->BeginTag('option', array('value' => 'UniqueID'));
							$Templater->Append('Unique ID');
						$Templater->EndLastTag();
						$Templater->BeginTag('option', array('selected' => 'selected', 'value' => 'PluginName'));
							$Templater->Append('Title');
						$Templater->EndLastTag();
						$Templater->BeginTag('option', array('value' => 'Author'));
							$Templater->Append('Author');
						$Templater->EndLastTag();
					$Templater->EndLastTag();
					$Templater->BeginTag('input', array('type' => 'Submit', 'name' => 'Search', 'value' => 'Search'), true);
				$Templater->EndLastTag();
			$Templater->EndLastTag();

			$Templater->BeginTag('hr', array(), true);
		$Templater->EndLastTag();
	}
}
?>