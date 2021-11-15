<?php

namespace Check24\OrderImport\Model\Task\Model;

class ProcessOrderResult
{
    /**
     * @var int
     */
    private $amountProcessedOrders;
    /**
     * @var int
     */
    private $amountFailedOrders;

    public function __construct(
        int $amountProcessedOrders,
        int $amountFailedOrders
    )
    {
        $this->amountProcessedOrders = $amountProcessedOrders;
        $this->amountFailedOrders = $amountFailedOrders;
    }

    public function getAmountProcessedOrders(): int
    {
        return $this->amountProcessedOrders;
    }

    public function getAmountFailedOrders(): int
    {
        return $this->amountFailedOrders;
    }
}
