<?php
namespace Twinsen\TradingIG\Login;


class LoginV3 extends AbstractLogin
{


    public function getHeaders()
    {

    }

    public function renewToken()
    {

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


    public function loginV3($apiKey, $securityToken, $accountId)
    {
        $this->securityToken = $securityToken;
        $this->apiKey = $apiKey;
        $this->accountId = $accountId;
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


}