<?php

namespace Check24Shopping\OrderImport\Observer;

use Check24Shopping\OrderImport\Api\Check24CancelRepositoryInterface;
use Check24Shopping\OrderImport\Api\Data\Check24CancelInterfaceFactory;
use Check24Shopping\OrderImport\Api\DynamicConfigRepositoryInterface;
use Check24Shopping\OrderImport\Model\OrderMappingRepository;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class OrderCancelAfter implements ObserverInterface
{
    /** @var OrderMappingRepository */
    private $orderMappingRepository;
    /** @var Check24CancelRepositoryInterface */
    private $check24CancelRepository;
    /** @var Check24CancelInterfaceFactory */
    private $cancelInterfaceFactory;
    /**
     * @var DynamicConfigRepositoryInterface
     */
    private $dynamicConfigRepository;

    public function __construct(
        OrderMappingRepository           $orderMappingRepository,
        Check24CancelRepositoryInterface $check24CancelRepository,
        Check24CancelInterfaceFactory    $cancelInterfaceFactory,
        DynamicConfigRepositoryInterface $dynamicConfigRepository
    )
    {
        $this->orderMappingRepository = $orderMappingRepository;
        $this->check24CancelRepository = $check24CancelRepository;
        $this->cancelInterfaceFactory = $cancelInterfaceFactory;
        $this->dynamicConfigRepository = $dynamicConfigRepository;
    }

    public function execute(Observer $observer)
    {
        if ($this->dynamicConfigRepository->load()->getSendCancel() === false) {
            return;
        }
        $order = $observer->getEvent()->getOrder();
        $mappingOrder = $this->orderMappingRepository->findByOrderId($order->getId());
        if (empty($mappingOrder) || $order->getState() !== 'canceled') {
            return;
        }
        $cancel = $this
            ->cancelInterfaceFactory
            ->create()
            ->setOrderId($order->getId());
        $this
            ->check24CancelRepository
            ->save(
                $cancel
            );
    }
}
