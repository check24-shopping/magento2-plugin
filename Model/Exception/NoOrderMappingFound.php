<?php

namespace Check24Shopping\OrderImport\Model\Exception;

use Exception;
use Throwable;

class NoOrderMappingFound extends Exception
{
    public function __construct(
        $message = "No mapping to Magento order found",
        $code = 0,
        Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
