<?php

namespace Twinsen\TradingIG;


use Twinsen\TradingIG\Login\LoginInterface;

class Api
{
    const DATE_FORMAT = "Y-m-d\TH:i:s";
    /**
     * @var LoginInterface
     */
    private $login;


    public function __construct(LoginInterface $login)
    {

        $this->login = $login;
    }

    /**
     * @param $epic string
     * @param $startDate \DateTime
     * @param $endDate \DateTime
     */
    public function getPrices($epic, $startDate, $endDate)
    {


        $command = $this->createCommand('GetPrices',
            array(
                'epic' => $epic,
                'startDate' => $startDate->format(self::DATE_FORMAT),
                "endDate" => $endDate->format(self::DATE_FORMAT)
            ));
        $iterator = $this->login->getClient()->getIterator($command);
        $retArray = array();
        foreach ($iterator as $responseModel) {
            $retArray[] = $responseModel;
        }

        //$responseModel = $this->client->execute($command);
        //echo $this->debugCommand($command);
        return $retArray;
    }

    /**
     * @param $commandName
     * @param $commandArray
     * @return \Guzzle\Service\Command\CommandInterface
     */
    public function createCommand($commandName, $commandArray)
    {
        $command = $this->login->getClient()->getCommand($commandName, $commandArray);
        $command->set("command.headers", $this->login->getHeaders());
        return $command;
    }

    public function getMarkets($hierarchyId)
    {
        $command = $this->createCommand('GetMarkets', array("hierarchyid" => $hierarchyId));
        $result = $this->login->getClient()->execute($command);
        echo "Url:" . $command->getRequest()->getUrl() . "\r\n";
        return $result;
    }

    public function getAllMarkets($hierarchyId = "")
    {
        $retArray = array();
        $stack = new \SplStack();
        $stack->push($hierarchyId);
        do {

            $result = $this->getMarkets($stack->pop());
            if ($result["nodes"]) {
                foreach ($result["nodes"] as $node) {
                    $stack->push($node["id"]);
                }
            }

            if ($result["markets"]) {
                $retArray = array_merge($retArray, $result["markets"]);
            }
            sleep(3);


        } while ($stack->count());


        return $retArray;
    }


}