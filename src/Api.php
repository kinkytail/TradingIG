<?php

namespace Twinsen\TradingIG;

use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Service\Client;
use Guzzle\Service\Resource\ResourceIteratorClassFactory;

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
        $resourceIteratorFactory = new ResourceIteratorClassFactory(array(
            "Twinsen\TradingIG\Iterators"
        ));
        $this->client->setResourceIteratorFactory($resourceIteratorFactory);


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
        $securityToken = $responseModel["oauthToken"]["access_token"];
        return $securityToken;
    }

    public function login($apiKey, $securityToken, $accountId)
    {
        $this->securityToken = $securityToken;
        $this->apiKey = $apiKey;
        $this->accountId = $accountId;
        $command = $this->createCommand('checkSession',array());
        $retVal = true;
        try {
            $responseModel = $this->client->execute($command);
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $clientErrorResponseException) {
            if ($clientErrorResponseException->getResponse()->getStatusCode() == 401) {
                $retVal = false;
            } else {
                throw $clientErrorResponseException;
            }
        }
        return $retVal;


    }


    /**
     * @param $epic string
     * @param $startDate \DateTime
     * @param $endDate \DateTime
     */
    public function getPrices($epic, $startDate, $endDate)
    {


        $command = $this->createCommand('GetPrices', array('epic' => $epic, 'startDate' => $this->formatDate($startDate), "endDate" => $this->formatDate($endDate)));
        $iterator = $this->client->getIterator($command);
        $retArray = array();
        foreach ($iterator as $responseModel) {
            $retArray[] = $responseModel;
        }

        //$responseModel = $this->client->execute($command);
        //echo $this->debugCommand($command);
        return $retArray;
    }

    public function createCommand($commandName, $commandArray)
    {
        $command = $this->client->getCommand($commandName, $commandArray);
        $command->set("command.headers", array(
            "X-IG-API-KEY" => $this->apiKey,
            "IG-ACCOUNT-ID" => $this->accountId,
            "Authorization" => "Bearer " . $this->securityToken,
            //"Version" => "3"
        ));
        return $command;
    }

    public function getMarkets($hierarchyId)
    {
        $command = $this->createCommand('GetMarkets', array("hierarchyid" => $hierarchyId));
        //$command->set("command.headers", array(
        //    "Version" => "1"
        //));
        $result = $command->execute();
        return $result;
    }

    public function getAllMarkets($hierarchyId)
    {
        $retArray = array();
        $stack = new \SplStack();
        do {

            $result = $this->getMarkets($hierarchyId);
            foreach ($result["nodes"] as $node) {
                $stack->push($node["id"]);
            }
            if($result["markets"]){
                $retArray = array_merge($retArray, $result["markets"]);
            }
            sleep(2);



        } while ($stack->count());


        return $retArray;
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