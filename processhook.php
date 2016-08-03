<?php
require_once 'helpers/githubapihelper.php';

// Split signature into algorithm and hash
list($Algorithm, $DeliveredHash) = explode('=', getallheaders()['X-Hub-Signature'], 2);

// Calculate hash based on payload and the secret
$CalculatedHash = hash_hmac($Algorithm, $_POST['payload'], GitHubAPI::OAUTH_CLIENT_SECRET);

// Check if hashes are equivalent
if ($DeliveredHash !== $CalculatedHash)
{
	// Doesn't work yet? Maybe encoding?
	// http_response_code(400);
	// die("The delivered HMAC digest did not match the calculated hash, you hacker.");
}

$Data = json_decode($_POST['payload'], true);
try
{
	GitHubAPI::ProcessRepositoryProperties($Data['repository']['id']);
}
catch (Exception $NoID)
{
	// Probably a ping event?
}
?>