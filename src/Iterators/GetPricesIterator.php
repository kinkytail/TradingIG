<?php
namespace Twinsen\TradingIG\Iterators;

use Guzzle\Service\Resource\ResourceIterator;

/**
 * Iterate over a get_users command
 */
class GetPricesIterator extends ResourceIterator
{
    protected function sendRequest()
    {
        if ($this->nextToken) {
            $this->command->set('pageNumber', $this->nextToken);
        }
        $result = $this->command->execute();
        $pageNumber = $result["metadata"]["pageData"]["pageNumber"];
        $totalPages = $result["metadata"]["pageData"]["totalPages"];
        if ($pageNumber < $totalPages) {

            $this->nextToken = $pageNumber + 1;
        } else {
            $this->nextToken = false;
        }


        return $result["prices"];
    }
}