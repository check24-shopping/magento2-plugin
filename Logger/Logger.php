<?php

namespace Check24Shopping\OrderImport\Logger;

use Check24Shopping\OrderImport\Helper\Config\OrderConfig;

class Logger extends \Monolog\Logger
{
    /** @var OrderConfig */
    private $orderConfig;

    public function __construct(
        OrderConfig $orderConfig,
                    $handlers = [],
                    $processors = []
    )
    {
        parent::__construct('check24orderimport.log', $handlers, $processors);

        $this->orderConfig = $orderConfig;
    }

    public function addRecord(int $level, string $message, array $context = [], ?\Monolog\DateTimeImmutable $datetime = NULL): bool
    {
        if ($level < 300 && !$this->orderConfig->isDebugEnabled()) {
            return false;
        }

        return parent::addRecord($level, $message, $context);
    }
}
