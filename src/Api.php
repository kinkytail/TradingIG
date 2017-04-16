<?php

namespace Twinsen\TradingIG;

use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Service\Client;

class Api
{

    /**
     * @var Client
     */
    protected $client;
    protected $apiKey;
    protected $securityToken;
    protected $accountId;

    public function __construct()
    {


        $path = realpath(dirname(__FILE__));
        $description = ServiceDescription::factory($path . '/api.json');
        $this->client = new Client();
        $this->client->setDescription($description);


    }

    /**
     * @param $login
     * @param $password
     * @param $apiKey
     * @return string
     */
    public function getSecurityToken($login, $password, $apiKey)
    {
        $command = $this->client->getCommand('Login', array('identifier' => $login, 'password' => $password, "apiKey" => $apiKey));
        $command->set("command.headers", array("Content-type" => "application/json"));
        $responseModel = $this->client->execute($command);
        return $responseModel;
    }

    public function login($apiKey, $securityToken, $accountId)
    {
        $this->securityToken = $securityToken;
        $this->apiKey = $apiKey;
        $this->accountId = $accountId;
    }


    /**
     * @param $epic string
     * @param $startDate \DateTime
     * @param $endDate \DateTime
     */
    public function getPrices($epic, $startDate, $endDate)
    {
        $command = $this->client->getCommand('GetPrices', array('epic' => $epic, 'startDate' => $this->formatDate($startDate), "endDate" => $this->formatDate($endDate)));
        $command->set("command.headers", array(
            "X-IG-API-KEY" => $this->apiKey,
            "IG-ACCOUNT-ID" => $this->accountId,
            "Authorization" => "Bearer " . $this->securityToken,
            "Version" => "3"
        ));
        $responseModel = $this->client->execute($command);
        //echo $this->debugCommand($command);
        return $responseModel;
    }

    protected function formatDate($date)
    {
        return $date->format("Y-m-d\TH:i:s");
    }

    /**
     * @param $command CommandInterface
     */
    protected function debugCommand($command)
    {
        $request = $command->getRequest();
        $response = $command->getResponse();
        $label = "Debug Output for Command:";
        $message = $label . PHP_EOL . implode(PHP_EOL, array(
                '[status code] ' . $response->getStatusCode(),
                '[reason phrase] ' . $response->getReasonPhrase(),
                '[url] ' . $request->getUrl(),
                '[request] ' . (string)$request,
                '[response] ' . (string)$response
            ));
        return $message;
    }
}