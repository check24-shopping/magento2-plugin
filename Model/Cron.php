<?php

namespace Check24Shopping\OrderImport\Model;

use Check24Shopping\OrderImport\Logger\Logger;
use Check24Shopping\OrderImport\Model\Exception\CustomerMessageInterface;
use Check24Shopping\OrderImport\Model\Task\ExportProductsTask;
use Check24Shopping\OrderImport\Model\Task\ImportOrderTask;
use Check24Shopping\OrderImport\Model\Task\ProcessCancelTask;
use Check24Shopping\OrderImport\Model\Task\ProcessOrderTask;
use Check24Shopping\OrderImport\Model\Task\ProcessReturnTask;
use Check24Shopping\OrderImport\Model\Task\SendCancelResponseTask;
use Check24Shopping\OrderImport\Model\Task\SendDispatchNotificationTask;
use Check24Shopping\OrderImport\Model\Task\SendOrderResponseTask;
use Magento\Framework\ObjectManagerInterface;

class Cron
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
            $this->objectManager->get(ImportOrderTask::class)->execute();
        } catch (CustomerMessageInterface $exception) {
            $this->logger->error($exception->getCustomerMessage());
        }
        try {
            $this->objectManager->get(ProcessOrderTask::class)->processNotProcessedOrders();
        } catch (CustomerMessageInterface $exception) {
            $this->logger->error($exception->getCustomerMessage());
        }
        try {
            $this->objectManager->get(ProcessCancelTask::class)->processNotProcessedOrders();
        } catch (CustomerMessageInterface $exception) {
            $this->logger->error($exception->getCustomerMessage());
        }
        try {
            $this->objectManager->get(SendOrderResponseTask::class)->sendNotRespondedOrders();
        } catch (CustomerMessageInterface $exception) {
            $this->logger->error($exception->getCustomerMessage());
        }
        try {
            $this->objectManager->get(SendDispatchNotificationTask::class)->submit();
        } catch (CustomerMessageInterface $exception) {
            $this->logger->error($exception->getCustomerMessage());
        }
        try {
            $this->objectManager->get(SendCancelResponseTask::class)->submit();
        } catch (CustomerMessageInterface $exception) {
            $this->logger->error($exception->getCustomerMessage());
        }
        try {
            $this->objectManager->get(ExportProductsTask::class)->exportProducts();
        } catch (CustomerMessageInterface $exception) {
            $this->logger->error($exception->getCustomerMessage());
        }
        try {
            $this->objectManager->get(ProcessReturnTask::class)->sendNotSendReturns();
        } catch (CustomerMessageInterface $exception) {
            $this->logger->error($exception->getCustomerMessage());
        }
    }
}
