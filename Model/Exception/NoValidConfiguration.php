<?php

namespace Check24\OrderImport\Model\Exception;

use Exception;

class NoValidConfiguration extends Exception implements CustomerMessageInterface
{
    public function getCustomerMessage(): string
    {
        return 'No active/valid configuration found';
    }
}
