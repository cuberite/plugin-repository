<?php
require_once 'templates/header.php';

class Templater
{
	private $Result = '<!DOCTYPE html>';
	private $ClosingTags = array();

	function __construct()
	{
		$this->BeginTag('html');
		$this->BeginTag('head');
			$this->BeginTag('link', array('type' => 'text/css', 'href' => 'css.css', 'rel' => 'stylesheet'), true);
			$this->BeginTag('title');
				$this->Append('MCServer Plugins');
			$this->EndLastTag();
		$this->EndLastTag();

		$this->BeginTag('body');
		HeaderTemplate::AddHeader($this);
	}

	function __destruct()
	{
		$this->EndLastTag(); // </body>
		$this->EndLastTag(); // </html>
		echo $this->Result;
	}

	function Append($HTMLString)
	{
		$this->Result = $this->Result . $HTMLString;
	}

	function BeginTag($TagName, $TagData = array(), $IsSelfClosing = false)
	{
		$HTMLTag = '<' . $TagName;
		foreach ($TagData as $Key => $Value)
		{
			$HTMLTag = $HTMLTag . ' ' . $Key . '="' . $Value . '"';
		}
		$this->Append($HTMLTag . '>' . "\n");

		if (!$IsSelfClosing)
		{
			$this->ClosingTags[] = '</' . $TagName . '>' . "\n";
		}
	}

	function EndLastTag()
	{
		$this->Append(array_pop($this->ClosingTags));
	}

	function SetRedirect($RedirectAddress = '/', $Timeout = 1)
	{
		header("Refresh: $Timeout; URL=$RedirectAddress");
	}
}
?>