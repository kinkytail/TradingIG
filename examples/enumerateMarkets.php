<?php
include_once "../vendor/autoload.php";
include_once "credentials.php";

use League\Csv\Writer;

$igApi = new Twinsen\TradingIG\Api();

if (!$igApi->login($apiKey, $accessToken, $accountId)) {
    $accessToken = $igApi->getSecurityToken($login, $password, $apiKey);

    $igApi->login($apiKey, $accessToken, $accountId);
}


$csv = Writer::createFromFileObject(new SplTempFileObject());

$prices = $igApi->getAllMarkets("600346");
foreach ($prices as $market) {
    $csv->insertOne($market);
}

file_put_contents('market.csv', $csv->__toString());