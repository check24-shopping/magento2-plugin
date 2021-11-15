<?php

namespace Check24\OrderImport\Model\Exception;

use Check24\OrderImport\Model\ValueObject\Interfaces\ErrorMessageInterface;
use Exception;
use Throwable;

class CanNotParseXml extends Exception implements CustomerMessageInterface, ErrorMessageInterface
{
    /** @var string */
    private $orderNumber;
    /** @var string */
    private $documentNumber;

    public function __construct(
        string    $orderNumber,
        string    $documentNumber,
                  $message = "No mapping to Magento order found",
                  $code = 0,
        Throwable $previous = null
    )
    {
        $this->orderNumber = $orderNumber;
        $this->documentNumber = $documentNumber;
        parent::__construct($message, $code, $previous);
    }

    public function getCustomerMessage(): string
    {
        return 'Fehler in der XML Datei';
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getDocumentNumber(): string
    {
        return $this->documentNumber;
    }

    public function getErrorMessage(): string
    {
        return (string)$this->getMessage();
    }
}
