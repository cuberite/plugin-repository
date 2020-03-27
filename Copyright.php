<?php
session_start();

require_once 'Globals.php';
require_once 'Environment Interfaces/Session.php';

$AuthorDetails = Session::GetLoggedInDetails();
$Templater = new \Twig\Environment(GetTwigLoader(), GetTwigOptions());
$Templater->display('Copyright.html', array('LoginDetails' => $AuthorDetails));
?>