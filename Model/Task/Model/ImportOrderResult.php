<?php

namespace Check24\OrderImport\Model\Task\Model;

class ImportOrderResult
{
    /**
     * @var int
     */
    private $amountImportedOrders;
    /**
     * @var int
     */
    private $amountSkippedOrders;

    public function __construct(
        int $amountImportedOrders,
        int $amountSkippedOrders
    )
    {
        $this->amountImportedOrders = $amountImportedOrders;
        $this->amountSkippedOrders = $amountSkippedOrders;
    }

    public function getAmountImportedOrders(): int
    {
        return $this->amountImportedOrders;
    }

    public function getAmountSkippedOrders(): int
    {
        return $this->amountSkippedOrders;
    }
}
