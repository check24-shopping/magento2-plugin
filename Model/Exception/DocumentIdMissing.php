<?php

namespace Check24\OrderImport\Model\Exception;

use Exception;

class DocumentIdMissing extends Exception implements CustomerMessageInterface
{
    public function getCustomerMessage(): string
    {
        return 'Document is faulty';
    }
}
