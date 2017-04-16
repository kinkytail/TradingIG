<?php
namespace Twinsen\TradingIG\Login;

use Guzzle\Service\Command\CommandInterface;
use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Service\Client;
use Guzzle\Service\Resource\ResourceIteratorClassFactory;

class AbstractLogin
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $path = realpath(dirname(__FILE__));
        $description = ServiceDescription::factory($path . '/../api.json');
        $this->client = new Client();
        $this->client->setDescription($description);
        $resourceIteratorFactory = new ResourceIteratorClassFactory(array(
            "Twinsen\TradingIG\Iterators"
        ));
        $this->client->setResourceIteratorFactory($resourceIteratorFactory);
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

    public function getClient()
    {
        return $this->client;
    }
}