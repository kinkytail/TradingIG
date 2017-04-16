<?php
include_once "../vendor/autoload.php";
include_once "credentials.php";

$igApi = new Twinsen\TradingIG\Api();
$loginResult = $igApi->getSecurityToken($login, $password, $apiKey);
echo "AccountID: " . $loginResult["accountId"]."\r\n";
echo "Access Token: " . $loginResult["oauthToken"]["access_token"]."\r\n";
