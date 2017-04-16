<?php
namespace Twinsen\TradingIG\Login;

use Guzzle\Http\Client;

interface LoginInterface
{
    /**
     * @return Client
     */
    public function getClient();

    /**
     * @return array
     */
    public function getHeaders();
}