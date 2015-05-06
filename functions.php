<?php
	
if (!($INIParseResult = parse_ini_file('configuration.ini')))
{
	header('Location: setup.php');
	return;
}

define('DB_ADDRESS', $INIParseResult['DatabaseAddress']);
define('DB_USERNAME', $INIParseResult['DatabaseUsername']);
define('DB_PASSWORD', $INIParseResult['DatabasePassword']);
define('DB_PLUGINSDATABASENAME', $INIParseResult['PluginDatabaseName']);

const IMMERSIVE_INFO = 0;
const IMMERSIVE_ERROR = 1;

function GetAndVerifyPostData(&$Variable, $TableName, $SQLLink)
{
	$EscapedInput = $SQLLink->real_escape_string($_POST[$TableName]);
	if (empty($EscapedInput))
	{
		$Variable = null;
		return true;
	}

	$Variable = $EscapedInput;
	return false;
}

function StoreAndSerialiseImages($TemporaryName, $GivenName, $UniqueID)
{
	if (array_filter($TemporaryName))
	{
		@mkdir('uploads' . DIRECTORY_SEPARATOR . $UniqueID, 0777, true);
		$Names = array();
		foreach ($TemporaryName as $Index => $Value)
		{
			$Name = "uploads/$UniqueID/" . SanitiseString(str_replace(" ", "_", $GivenName[$Index])); // No usage of DIRECTORY_SEPARATOR because other parts of PHP do not like Windows' backslashes
			$Names[$Index] = $Name;
			move_uploaded_file($Value, $Name);
		}
		return serialize($Names);
	}
	return '';
}

function StoreFile($TemporaryName, $GivenName, $UniqueID)
{
	if (!empty($TemporaryName))
	{
		@mkdir('uploads' . DIRECTORY_SEPARATOR . $UniqueID, 0777, true);
		$Name = "uploads/$UniqueID/" . SanitiseString(str_replace(" ", "_", $GivenName)); // No usage of DIRECTORY_SEPARATOR because other parts of PHP do not like Windows' backslashes
		move_uploaded_file($TemporaryName, $Name);
		return $Name;
	}
	return '';
}

function SanitiseString($String)
{
   return trim(preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), $String));
}

function RecursivelyDeleteDirectory($Directory)
{
	try
	{
		$Files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($Directory, RecursiveDirectoryIterator::SKIP_DOTS),
			RecursiveIteratorIterator::CHILD_FIRST
		);
	}
	catch (Exception $Exception)
	{
		return;
	}

	foreach($Files as $File)
	{
		if ($File->isDir())
		{
			rmdir($File->getRealPath());
		}
		else
		{
			unlink($File->getRealPath());
		}
	}
	rmdir($Directory);
}