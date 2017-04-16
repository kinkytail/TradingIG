<?php
include_once "../vendor/autoload.php";
include_once "credentials.php";


$igLogin = new Twinsen\TradingIG\Login\LoginV2();
$igLogin->login($login,$password,$apiKey);


$igApi = new Twinsen\TradingIG\Api($igLogin);

$startDate = DateTime::createFromFormat("d.m.Y H:i:s", '04.04.2017 12:00:00');
$endDate = clone $startDate;
$endDate->modify('+ 2hour');

$prices = $igApi->getPrices("IX.D.DAX.IFD.IP", $startDate, $endDate);
var_dump($prices);
//$sessionId = $igApi->login($login, $password, $apiKey);
//var_dump($sessionId);