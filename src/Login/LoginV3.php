<?php

namespace Twinsen\TradingIG\Login;


class LoginV3 extends AbstractLogin implements LoginInterface
{
    protected $apiKey;
    protected $securityToken;
    protected $accountId;

    public function getHeaders()
    {
        return array(
            "X-IG-API-KEY" => $this->apiKey,
            "IG-ACCOUNT-ID" => $this->accountId,
            "Authorization" => "Bearer " . $this->securityToken,
            "version" => 3
        );

    }

    public function renewToken()
    {
        $command = $this->createCommand('checkSession', array());
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


    public function login($login, $password, $apiKey)
    {

        $command = $this->client->getCommand('Login', array('identifier' => $login, 'password' => $password, "apiKey" => $apiKey));
        $command->set("command.headers", array("Content-type" => "application/json"));
        $responseModel = $this->client->execute($command);
        $securityToken = $responseModel["oauthToken"]["access_token"];
        $this->securityToken = $securityToken;
        $this->apiKey = $apiKey;
        $this->accountId = $responseModel["accountId"];
        return $securityToken;
    }


}