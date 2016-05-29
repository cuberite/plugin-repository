<?php
require_once 'templates/header.php';
require_once 'templates/footer.php';

class Templater
{
	private $ClosingTags = array();

	function __construct()
	{
		$this->BeginTag('!DOCTYPE html', array(), true);
		$this->BeginTag('html');
		$this->BeginTag('head');
			$this->BeginTag('link', array('type' => 'text/css', 'href' => 'css.css', 'rel' => 'stylesheet'), true);
			$this->BeginTag('title');
				$this->Append('Cuberite Plugins');
			$this->EndLastTag();
		$this->EndLastTag();

		$this->BeginTag('body');
		$this->BeginTag('div', array('id' => 'wrapper'));
		HeaderTemplate::AddHeader($this);
		$this->BeginTag('div', array('id' => 'content'));
	}

	function __destruct()
	{
		$this->EndLastTag(); // </div id="content">
		FooterTemplate::AddFooter($this);
		$this->EndLastTag(); // </div id="wrapper">
		$this->EndLastTag(); // </body>
		$this->EndLastTag(); // </html>
	}

	function Append($HTMLString)
	{
		echo $HTMLString;
		flush();
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

	function SetRedirect($RedirectAddress = '/')
	{
		header("Location: $RedirectAddress");
	}

	function SetRefresh($RedirectAddress = '/', $Timeout = 1)
	{
		header("Refresh: $Timeout; URL=$RedirectAddress");
	}
}
?>
