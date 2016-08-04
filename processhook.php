<?php
require_once 'helpers/githubapihelper.php';
require_once 'functions.php';

// Ensure appropriate header and payload are present
if (!isset(getallheaders()['X-Hub-Signature']) || !isset($_POST['payload']))
{
	http_response_code(400);
	return; // Probably direct navigation to this file
}

// Split signature into algorithm and hash
$SplitHeader = explode('=', getallheaders()['X-Hub-Signature'], 2);

// Count number of elements in array to make sure it's as expected
if (count($SplitHeader) !== 2)
{
	http_response_code(400);
	die("You're not from GitHub are you, you hacker.");
}

// Pipe to variables
list($Algorithm, $DeliveredHash) = $SplitHeader;

// Calculate hash based on RAW payload and the secret
$CalculatedHash = hash_hmac($Algorithm, file_get_contents('php://input'), GH_OAUTH_CLIENT_SECRET);

// Check if hashes are equivalent
// NB: if $Algorithm ∉ { hash_algos() }, $Algorithm === FALSE, and so $DeliveredHash, STRING will not be type-equivalent to FALSE
if ($DeliveredHash !== $CalculatedHash)
{
	http_response_code(400);
	die("The delivered HMAC digest did not match the calculated hash, you hacker.");
}

// Decode the PROCESSED payload, assuming that the request is genuine and the body is valid JSON
$Data = json_decode($_POST['payload'], true);
try
{
	GitHubAPI::ProcessRepositoryProperties($Data['repository']['id']);
}
catch (Exception $NoID)
{
	// Probably a ping event?
	http_response_code(204);
	return;
}

http_response_code(204);
?>