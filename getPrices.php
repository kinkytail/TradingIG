<?php
include_once "vendor/autoload.php";
include_once "login.php";

$igApi = new Twinsen\TradingIG\Api();
$sessionId = $igApi->login($login, $password, $apiKey);
var_dump($sessionId);