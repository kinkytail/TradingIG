<?php

namespace Twinsen\TradingIG;

use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Service\Client;

class Api
{

    /**
     * @var Client
     */
    protected $client;

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
    public function login($login, $password, $apiKey)
    {
        $command = $this->client->getCommand('Login', array('identifier' => $login, 'password' => $password, "apiKey" => $apiKey));
        $command->set("command.headers", array("Content-type" => "application/json"));
        $responseModel = $this->client->execute($command);
        return $responseModel;
    }
}