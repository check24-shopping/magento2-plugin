<?php

namespace Check24Shopping\OrderImport\Model;

use Check24Shopping\OrderImport\Logger\Logger;
use Check24Shopping\OrderImport\Model\Exception\CustomerMessageInterface;
use Check24Shopping\OrderImport\Model\Task\ExportProductsTask;
use Magento\Framework\ObjectManagerInterface;

class ExportCron
{
    /** @var ObjectManagerInterface */
    private $objectManager;
    /** @var Logger */
    private $logger;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Logger                 $logger
    )
    {
        $this->objectManager = $objectManager;
        $this->logger = $logger;
    }

    public function execute()
    {
        sleep(rand(0, 30));
        try {
            $this->objectManager->get(ExportProductsTask::class)->exportProducts();
        } catch (CustomerMessageInterface $exception) {
            $this->logger->error($exception->getCustomerMessage());
        }
    }
}
