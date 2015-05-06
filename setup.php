<?php

class Configurator
{
	static function EchoHTML($ShouldDeleteDatabase)
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
			<?php

		if ($ShouldDeleteDatabase)
		{
			?>
				<style>		
					#buttonenclosure { position: absolute; text-align: center; height: 90px; width: 350px; bottom: 10px; }
				</style>
				<div id="buttonenclosure">
					<input type="Submit" value="Delete database" name="DeleteDatabase"/>
				</div>
			<?php
		}
		else
		{
			?>
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
				
				<div id="buttonenclosure">
					<input type="Submit" value="Submit" name="Submit"/>
				</div>
			<?php
		}
		
		?>
			</form>
		</div>
	</body>
</html>
		<?php
	}
	
	private static function DeleteDatabase()
	{
		require_once 'functions.php';
		
		$SQLLink = new mysqli(DB_ADDRESS, DB_USERNAME, DB_PASSWORD, DB_PLUGINSDATABASENAME);
		if ($SQLLink->connect_errno)
		{
			echo "Failure connecting to database: " . $SQLLink->error;
			return false;
		}
		
		if (!($Query = $SQLLink->query("DROP DATABASE " . DB_PLUGINSDATABASENAME)))
		{
			echo 'Failure dropping database: ' . $SQLLink->error;
			return false;
		}
		
		return true;
	}
	
	static function DeleteDatabaseAndConfig()
	{
		if (Configurator::DeleteDatabase())
		{		
			if (!unlink('configuration.ini'))
			{
				echo 'Failure deleting configuration file. Please do so manually.';
				return false;
			}
			
			return true;
		}
		
		return false;
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

		// Create the accounts table
		$Query = $SQLLink->query('CREATE TABLE IF NOT EXISTS Accounts (
			Username VARCHAR(255) NOT NULL,
			Password VARCHAR(255) NOT NULL
			)'
		);
		if (!$Query)
		{
			echo "Failure creating the accounts table: " . $SQLLink->error;
			return false;
		}

		// Creates the plug-in data table
		$Query = $SQLLink->query('CREATE TABLE IF NOT EXISTS PluginData (
			UniqueID INT AUTO_INCREMENT,
			Author VARCHAR(255) NOT NULL,
			AuthorDisplayName VARCHAR(255) NOT NULL,
			PluginName VARCHAR(255) NOT NULL,
			PluginDescription TEXT,
			PluginVersion VARCHAR(255) NOT NULL,
			Icon TEXT,
			Images TEXT,
			PluginFile TEXT,
			Comments TEXT,
			PRIMARY KEY(UniqueID)
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
			LinkedPluginUniqueID INT,
			Comment TEXT,
			PRIMARY KEY(UniqueID)
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
			$Configuration = '[SQL]
DatabaseAddress=' . $_POST['DBAddress'] . '
DatabaseUsername=' . $_POST['DBUsername'] . '
DatabasePassword=' . $_POST['DBPassword'] . '
PluginDatabaseName=' . $_POST['DBPluginDatabaseName'];

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

if (isset($_POST['DeleteDatabase']))
{
	if (Configurator::DeleteDatabaseAndConfig())
	{
		header('Refresh: 0');
		return;
	}
}
else if (isset($_POST['Submit']))
{
	if (Configurator::CreateDatabaseAndConfig())
	{
		header('Location: index.php');
		return;
	}
}

Configurator::EchoHTML(file_exists('configuration.ini'));

?>