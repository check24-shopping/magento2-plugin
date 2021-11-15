<?php

namespace Check24Shopping\OrderImport\Observer;

use Check24Shopping\OrderImport\Api\Check24ShipmentRepositoryInterface;
use Check24Shopping\OrderImport\Api\Data\Check24ShipmentInterfaceFactory;
use Check24Shopping\OrderImport\Api\DynamicConfigRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;

class OrderShipmentAfter implements ObserverInterface
{

    /**
     * @var Check24ShipmentRepositoryInterface
     */
    private $shipmentFactory;
    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;
    /**
     * @var DynamicConfigRepositoryInterface
     */
    private $dynamicConfigRepository;

    public function __construct(
        Check24ShipmentInterfaceFactory    $shipmentInterfaceFactory,
        Check24ShipmentRepositoryInterface $shipmentRepository,
        DynamicConfigRepositoryInterface   $dynamicConfigRepository
    )
    {
        $this->shipmentFactory = $shipmentInterfaceFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->dynamicConfigRepository = $dynamicConfigRepository;
    }

    public function execute(Observer $observer)
    {
        if ($this->dynamicConfigRepository->load()->getSendDispatch() === false) {
            return;
        }
        /** @var ShipmentInterface $shipment */
        $shipment = $observer->getEvent()->getShipment();
        $this->shipmentRepository
            ->save(
                $this
                    ->shipmentFactory
                    ->create()
                    ->setShipmentId($shipment->getEntityId())
            );
    }
}
