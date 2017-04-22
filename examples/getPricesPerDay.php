<?php
include_once "../vendor/autoload.php";
include_once "credentials.php";

use League\Csv\Writer;

$igLogin = new Twinsen\TradingIG\Login\LoginV3();
$igLogin->login($login,$password,$apiKey);


$igApi = new Twinsen\TradingIG\Api($igLogin);



$startDate = DateTime::createFromFormat("d.m.Y H:i:s", '04.04.2017 00:00:00');
$endDate = clone $startDate;
$endDate->modify('+ 1day');

$csv = Writer::createFromFileObject(new SplTempFileObject());

$prices = $igApi->getPrices("IX.D.DAX.IFD.IP", $startDate, $endDate);
foreach ($prices as $priceData) {
    $time = $priceData["snapshotTimeUTC"];
    $time = DateTime::createFromFormat("Y-m-d\TH:i:s", $time);
    $time = $time->format("d.m.Y H:i:s");

    $closePrice = $priceData["closePrice"]["bid"];
    $closePrice = str_replace(".",",",$closePrice);

    $csv->insertOne([$time, $closePrice]);
}

file_put_contents('dax.csv', $csv->__toString());