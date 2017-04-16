<?php
namespace Twinsen\TradingIG\Login;


class LoginV2 extends AbstractLogin implements LoginInterface
{


    /**
     * @var string
     */
    public $securityToken;

    /**
     * @var string
     */
    public $cst;

    /**
     * @var string
     */
    public $apiKey;

    /**
     * @param $login
     * @param $password
     * @param $apiKey
     * @return string
     */
    public function login($login, $password, $apiKey)
    {
        $command = $this->client->getCommand('Login',
            array(
                'identifier' => $login,
                'password' => $password,
                "apiKey" => $apiKey,
                "version" => 2
            )
        );
        $command->set("command.headers", array("Content-type" => "application/json"));
        $responseModel = $this->client->execute($command);
        $response = $command->getResponse();

        $header = $response->getHeaders();
        $cstArray = $header->get('CST');
        $securityArray = $header->get('X-SECURITY-TOKEN');

        $this->cst = $cstArray[0];

        $this->securityToken = $securityArray[0];
        $this->apiKey = $apiKey;
        return $responseModel;
    }

    public function getHeaders()
    {
        return array(
            "X-IG-API-KEY" => $this->apiKey,
            "X-SECURITY-TOKEN" => $this->securityToken,
            "CST" => $this->cst
        );
    }


}