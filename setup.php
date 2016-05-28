<?php

class Configurator
{
	static function EchoHTML()
	{
		?>
<!DOCTYPE html>
<html>
	<head>
		<title>Plugin Repository configuration</title>
	</head>
	<body>
		<style>
			label { display: inline-block; width: 170px; text-align: right; }
			#configurationbox
			{
				font-family: Segoe UI, Trebuchet MS;
				border: solid thin black;
				border-radius: 5px;
				padding: 10px;
				padding-top: 0px;
				width: 350px;
				height: 200px;
				top: calc(50% - 200px / 2);
				left: calc(50% - 350px / 2);
				position: absolute;
			}
		</style>
		
		<div id="configurationbox">
			<h2>Configuration</h2>
			<form method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
				<style>		
					#buttonenclosure { text-align: center; margin-top: 10px; }
				</style>
				<label>Database address:	</label>
				<input autofocus required placeholder="localhost" type="text" name="DBAddress"/><br/>
				
				<label>Database username:	</label>
				<input type="text" required name="DBUsername"/><br/>
				
				<label>Database password:	</label>
				<input type="text" name="DBPassword"/><br/>
				
				<label>Plugin database name:</label>
				<input type="text" required name="DBPluginDatabaseName"/><br/>
				
				<label>GitHub Application ID:</label>
				<input type="text" required name="GHClientID"/><br/>
				
				<label>GitHub Application Secret:</label>
				<input type="text" required name="GHClientSecret"/><br/>
				
				<div id="buttonenclosure">
					<input type="Submit" value="Submit" name="Submit"/>
				</div>
			</form>
		</div>
	</body>
</html>
		<?php
	}
	
	private static function CreateDatabase()
	{
		$SQLLink = new mysqli($_POST['DBAddress'], $_POST['DBUsername'], $_POST['DBPassword']);
		if ($SQLLink->connect_errno)
		{
			echo "Failure connecting to database: " . $SQLLink->error;
			return false;
		}

		// Create the database
		$Query = $SQLLink->query("CREATE DATABASE IF NOT EXISTS " . $_POST['DBPluginDatabaseName']);
		if (!$Query || !$SQLLink->select_db($_POST['DBPluginDatabaseName']))
		{
			echo "Failure creating database: " . $SQLLink->error;
			return false;
		}

		// Creates the plug-in data table
		$Query = $SQLLink->query('CREATE TABLE IF NOT EXISTS PluginData (
				RepositoryID INT NOT NULL,
				AuthorID INT NOT NULL,
				PRIMARY KEY(RepositoryID)
			)'
		);
		if (!$Query)
		{
			echo "Failure creating the plugin data table: " . $SQLLink->error;
			return false;
		}

		// Creates the comments table
		$Query = $SQLLink->query('CREATE TABLE IF NOT EXISTS Comments (
				UniqueID INT AUTO_INCREMENT,
				LinkedRepositoryID INT NOT NULL,
				Comment TEXT NOT NULL,
				AuthorID INT NOT NULL,
				PRIMARY KEY(UniqueID),
				FOREIGN KEY (LinkedRepositoryID) REFERENCES PluginData(RepositoryID) ON DELETE CASCADE
			)'
		);
		if (!$Query)
		{
			echo "Failure creating the comments table: " . $SQLLink->error;
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
DatabaseAddress=' . $_POST['DBAddress'] . '
DatabaseUsername=' . $_POST['DBUsername'] . '
DatabasePassword=' . $_POST['DBPassword'] . '
PluginDatabaseName=' . $_POST['DBPluginDatabaseName'] . '

[GitHub]
GitHubClientID=' . $_POST['GHClientID'] . '
GitHubClientSecret=' . $_POST['GHClientSecret'];

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
	header('Location:');
	return;
}

if (isset($_POST['Submit']))
{
	if (Configurator::CreateDatabaseAndConfig())
	{
		header('Location: index.php');
		return;
	}
}

Configurator::EchoHTML();

?>