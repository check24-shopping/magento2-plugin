<?php

namespace Check24Shopping\OrderImport\Service\Mapping;

use Check24Shopping\OrderImport\Api\Data\OrderMappingInterfaceFactory;
use Check24Shopping\OrderImport\Api\Data\OrderPositionMappingInterfaceFactory;
use Check24Shopping\OrderImport\Model\OrderMappingRepository;
use Check24Shopping\OrderImport\Model\OrderPositionMappingRepository;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderInterface;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderItemInterface;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\Xml\OpenTransOrderDocument;
use Exception;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class NewMappingService
{
    /** @var OrderMappingInterfaceFactory */
    private $orderMappingFactory;
    /** @var OrderMappingRepository */
    private $orderMappingRepository;
    /** @var OrderPositionMappingInterfaceFactory */
    private $orderPositionMappingInterfaceFactory;
    /** @var OrderPositionMappingRepository */
    private $orderPositionMappingRepository;

    public function __construct(
        OrderMappingInterfaceFactory         $orderMappingFactory,
        OrderMappingRepository               $orderMappingRepository,
        OrderPositionMappingInterfaceFactory $orderPositionMappingInterfaceFactory,
        OrderPositionMappingRepository       $orderPositionMappingRepository
    )
    {
        $this->orderMappingFactory = $orderMappingFactory;
        $this->orderMappingRepository = $orderMappingRepository;
        $this->orderPositionMappingInterfaceFactory = $orderPositionMappingInterfaceFactory;
        $this->orderPositionMappingRepository = $orderPositionMappingRepository;
    }

    public function save(OrderInterface $magentoOrder, OpenTransOrderDocument $orderDocument)
    {
        $this->orderMappingRepository
            ->save(
                $this
                    ->orderMappingFactory
                    ->create()
                    ->setCheck24OrderId($orderDocument->getOrderId())
                    ->setPartyDeliveryId($orderDocument->getDeliveryParty()->getId())
                    ->setPartySupplierId($orderDocument->getSupplierParty()->getId())
                    ->setPartyInvoiceIssuerId($orderDocument->getSupplierParty()->getId())
                    ->setPartnerId($orderDocument->getPartnerId())
                    ->setMagentoOrderId($magentoOrder->getId())
                    ->setMagentoOrderIncrementId($magentoOrder->getIncrementId())
            );
        foreach ($magentoOrder->getItems() as $item) {
            $orderItem = $this->getOpenTransItem($orderDocument, $item);
            $this->orderPositionMappingRepository
                ->save(
                    $this
                        ->orderPositionMappingInterfaceFactory
                        ->create()
                        ->setCheck24OrderId($orderDocument->getOrderId())
                        ->setCheck24PositionId($item->getAdditionalData())
                        ->setMagentoPositionId($item->getItemId())
                        ->setOrderUnit($orderItem->getUnit())
                );
        }
    }

    private function getOpenTransItem(
        OpenTransDataOrderInterface $openTransOrder,
        OrderItemInterface          $item
    ): OpenTransDataOrderItemInterface
    {
        foreach ($openTransOrder->getOrderItems() as $orderItem) {
            if ($item->getAdditionalData() === $orderItem->getId()) {
                return $orderItem;
            }
        }

        throw new Exception('Order item mapping not found');
    }
}
