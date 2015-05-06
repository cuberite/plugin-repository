<?php
session_start();

require_once 'functions.php';
require_once 'helpers/accountshelper.php';
require_once 'helpers/templater.php';

$Template = new Templater();
$SQLLink = new mysqli(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);

if (!AccountsHelper::GetLoggedInDetails($Details))
{
	DisplayHTMLMessage('You\'ll need to log in first!', 'login.php', true);
	return;
}
list($Username, $ProfileImageURL, $FullName, $DisplayName) = $Details;

?>

<article class="boundedbox plugin show infobox">
	<img class="boundedbox expandedicon show" style="margin-top: -20px" src=<?php echo '"' . $ProfileImageURL . '"' ?>>
	<figcaption class="boundedbox expandedicon caption">
		<?php echo 'Username: ' . $Username ?>
		<br>
		<?php echo 'Display Name: ' . $DisplayName ?>
	</figcaption>
	<h2><?php echo $FullName ?></h2>
	<hr>
	All personal details are changeable on Gravatar.
</article>

<nav class="boundedbox plugin add"><br>
	<form action=<?php echo $_SERVER['PHP_SELF'] ?> method="POST">
		<input style="height: 50px; margin: auto; display: block;" name="Delete" type="Submit" value="Close account">
	</form>
</nav>