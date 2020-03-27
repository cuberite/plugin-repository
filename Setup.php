<?php
class Configurator
{
	private static function CreateDatabase()
	{
		$SQLLink = new mysqli($_POST['DBHostname'], $_POST['DBUsername'], $_POST['DBPassword'], '', $_POST['DBPort']);
		if ($SQLLink->connect_errno)
		{
			echo 'Failure connecting to database: ' . $SQLLink->error;
			return false;
		}

		// Create the database
		$Query = $SQLLink->query('CREATE DATABASE IF NOT EXISTS `' . $_POST['DBPluginDatabaseName'] . '`');
		if (!$Query || !$SQLLink->select_db($_POST['DBPluginDatabaseName']))
		{
			echo 'Failure creating database: ' . $SQLLink->error;
			return false;
		}

		// Creates the authors table
		$Query = $SQLLink->query('CREATE TABLE IF NOT EXISTS Authors (
				AuthorId INT AUTO_INCREMENT,
				Login TEXT NOT NULL,
				DisplayName TEXT NOT NULL,
				AvatarHyperlink TEXT NOT NULL,
				PRIMARY KEY (AuthorId)
			)'
		);
		if (!$Query)
		{
			echo 'Failure creating the authors table: ' . $SQLLink->error;
			return false;
		}

		// Creates the plug-in data table
		$Query = $SQLLink->query('CREATE TABLE IF NOT EXISTS PluginData (
				RepositoryId INT NOT NULL,
				AuthorId INT NOT NULL,
				DownloadCount INT NOT NULL DEFAULT 0,
				UpdateHookId INT,

				RepositoryName TEXT NOT NULL,
				RepositoryFullName TEXT NOT NULL,
				RepositoryVersion TEXT,
				License TEXT,
				Description TEXT,
				Readme TEXT NOT NULL,
				IconHyperlink TEXT NOT NULL,

				PRIMARY KEY (RepositoryId),
				FOREIGN KEY (AuthorId) REFERENCES Authors(AuthorId) ON DELETE CASCADE
			)'
		);
		if (!$Query)
		{
			echo "Failure creating the plugin data table: " . $SQLLink->error;
			return false;
		}

		// Creates the plug-in download links table
		$Query = $SQLLink->query('CREATE TABLE IF NOT EXISTS DownloadHyperlinks (
				LinkId INT AUTO_INCREMENT,
				RepositoryId INT NOT NULL,
				Name TEXT NOT NULL,
				Tag TEXT NOT NULL,
				Hyperlink TEXT NOT NULL,

				PRIMARY KEY (LinkId),
				FOREIGN KEY (RepositoryId) REFERENCES PluginData(RepositoryId) ON DELETE CASCADE
			)'
		);
		if (!$Query)
		{
			echo "Failure creating the plugin data table: " . $SQLLink->error;
			return false;
		}

		// Creates the plug-in screenshot links table
		$Query = $SQLLink->query('CREATE TABLE IF NOT EXISTS ScreenshotHyperlinks (
				LinkId INT AUTO_INCREMENT,
				RepositoryId INT NOT NULL,
				Name TEXT NOT NULL,
				Hyperlink TEXT NOT NULL,

				PRIMARY KEY (LinkId),
				FOREIGN KEY (RepositoryId) REFERENCES PluginData(RepositoryId) ON DELETE CASCADE
			)'
		);
		if (!$Query)
		{
			echo "Failure creating the plugin data table: " . $SQLLink->error;
			return false;
		}

		// Creates the comments table
		$Query = $SQLLink->query('CREATE TABLE IF NOT EXISTS Comments (
				CommentId INT AUTO_INCREMENT,
				RepositoryId INT NOT NULL,
				Comment TEXT NOT NULL,
				CreationTime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				AuthorId INT NOT NULL,

				PRIMARY KEY (CommentId),
				FOREIGN KEY (RepositoryId) REFERENCES PluginData(RepositoryId) ON DELETE CASCADE,
				FOREIGN KEY (AuthorId) REFERENCES Authors(AuthorId) ON DELETE CASCADE
			)'
		);
		if (!$Query)
		{
			echo 'Failure creating the comments table: ' . $SQLLink->error;
			return false;
		}

		return true;
	}

	static function CreateDatabaseAndConfig()
	{
		if (Configurator::CreateDatabase())
		{
			$Configuration =
'[SQL]
DatabaseHostname="' . $_POST['DBHostname'] . '"
DatabasePort="' . $_POST['DBPort'] . '"
DatabaseUsername="' . $_POST['DBUsername'] . '"
DatabasePassword="' . $_POST['DBPassword'] . '"
PluginDatabaseName="' . $_POST['DBPluginDatabaseName'] . '"

[GitHub]
GitHubClientId="' . $_POST['GHClientID'] . '"
GitHubClientSecret="' . $_POST['GHClientSecret'] . '"';

			if (!($Handle = fopen('configuration.ini', 'w')) || !fwrite($Handle, $Configuration))
			{
				echo "Failure writing configuration file";
				return false;
			}

			return true;
		}

		return false;
	}
}

if (file_exists('configuration.ini'))
{
	header('Location: /');
	return;
}

if (isset($_POST['Submit']))
{
	if (Configurator::CreateDatabaseAndConfig())
	{
		header('Location: /');
		return;
	}
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Plugin Repository configuration</title>
	</head>
	<body>
		<style>
			label
			{
				display: inline-block;
				width: 170px;
				text-align: right;
			}
			#configuration-box
			{
				font-family: Segoe UI, Trebuchet MS;
				border: solid thin black;
				border-radius: 5px;
				padding: 10px;
				padding-top: 0px;
				width: 350px;
				height: 270px;
				top: calc(50% - 200px / 2);
				left: calc(50% - 350px / 2);
				position: absolute;
			}
			#button-enclosure
			{
				text-align: center;
				margin-top: 10px;
			}
		</style>

		<div id="configuration-box">
			<h2>Configuration</h2>
			<form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
				<label>Database hostname:	</label>
				<input autofocus required placeholder="localhost" type="text" name="DBHostname"/><br/>
				
				<label>Database port:	</label>
				<input autofocus required placeholder="3306" type="text" name="DBPort"/><br/>

				<label>Database username:	</label>
				<input type="text" required name="DBUsername"/><br/>

				<label>Database password:	</label>
				<input type="text" name="DBPassword"/><br/>

				<label>Plugin database name:</label>
				<input type="text" required name="DBPluginDatabaseName"/><br/>

				<label>GitHub Application ID:</label>
				<input type="text" required name="GHClientID"/><br/>

				<label>GitHub Client Secret:</label>
				<input type="text" required name="GHClientSecret"/><br/>

				<div id="button-enclosure">
					<input type="Submit" value="Submit" name="Submit"/>
				</div>
			</form>
		</div>
	</body>
</html>