<?php

namespace Check24Shopping\OrderImport\Model\Exception;

use Exception;
use Throwable;

class MissingMatchingStore extends Exception implements CustomerMessageInterface
{
    public function __construct($message = 'No matching store id', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getCustomerMessage(): string
    {
        return $this->message;
    }
}
