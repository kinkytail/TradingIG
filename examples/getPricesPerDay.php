<?php
include_once "../vendor/autoload.php";
include_once "credentials.php";

use League\Csv\Writer;
$igApi = new Twinsen\TradingIG\Api();
$loginResult = $igApi->getSecurityToken($login, $password, $apiKey);



$igApi = new Twinsen\TradingIG\Api();
$igApi->login($apiKey, $loginResult["oauthToken"]["access_token"], $accountId);

$startDate = DateTime::createFromFormat("d.m.Y H:i:s", '04.04.2017 00:00:00');
$endDate = clone $startDate;
$endDate->modify('+ 1day');

$csv = Writer::createFromFileObject(new SplTempFileObject());

$prices = $igApi->getPrices("IX.D.DAX.IFD.IP", $startDate, $endDate);
foreach($prices as $priceData){
    $time = $priceData["snapshotTime"];
    $closePrice = $priceData["closePrice"]["bid"];

    $csv->insertOne([$time,$closePrice]);
}

file_put_contents('dax.csv',$csv->__toString());