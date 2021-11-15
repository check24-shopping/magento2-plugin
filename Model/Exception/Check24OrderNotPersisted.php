<?php

namespace Check24Shopping\OrderImport\Model\Exception;

use Exception;

class Check24OrderNotPersisted extends Exception implements CustomerMessageInterface
{
    public function getCustomerMessage(): string
    {
        return 'Order could not be saved';
    }
}
