<?php
include_once "../vendor/autoload.php";
include_once "credentials.php";

use League\Csv\Writer;


$igLogin = new Twinsen\TradingIG\Login\LoginV2();
$igLogin->login($login,$password,$apiKey);


$igApi = new Twinsen\TradingIG\Api($igLogin);



$csv = Writer::createFromFileObject(new SplTempFileObject());

$prices = $igApi->getAllMarkets("600346");
foreach ($prices as $market) {
    $csv->insertOne($market);
}

file_put_contents('market.csv', $csv->__toString());