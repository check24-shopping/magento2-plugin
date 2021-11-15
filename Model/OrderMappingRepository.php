<?php

namespace Check24Shopping\OrderImport\Model;

use Check24Shopping\OrderImport\Api\Data\OrderMappingInterface;
use Check24Shopping\OrderImport\Api\Data\OrderMappingInterfaceFactory;
use Check24Shopping\OrderImport\Api\OrderMappingRepositoryInterface;
use Check24Shopping\OrderImport\Model\ResourceModel\OrderMapping\CollectionFactory as OrderMappingCollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class OrderMappingRepository implements OrderMappingRepositoryInterface
{
    /** @var OrderMappingInterfaceFactory */
    private $orderFactory;
    /** @var DateTimeFactory */
    private $dateTimeFactory;
    /** @var OrderMappingCollectionFactory */
    private $collectionFactory;

    public function __construct(
        OrderMappingInterfaceFactory  $orderFactory,
        DateTimeFactory               $dateTimeFactory,
        OrderMappingCollectionFactory $collectionFactory
    )
    {
        $this->orderFactory = $orderFactory;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->collectionFactory = $collectionFactory;
    }

    public function save(OrderMappingInterface $orderMapping)
    {
        if ($orderMapping->isObjectNew()) {
            $orderMapping->setCreatedAt($this->dateTimeFactory->create()->gmtDate());
        }

        return $this->load($orderMapping->getId())
            ->setData($orderMapping->getData())
            ->save();
    }

    public function load($id): ?OrderMappingInterface
    {
        return $this->orderFactory
            ->create()
            ->load($id);
    }

    public function delete(OrderMappingInterface $orderMapping)
    {
        return $this
            ->load($orderMapping->getId())
            ->delete();
    }

    public function findByOrderId(string $oderId): ?OrderMappingInterface
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(
            OrderMappingInterface::FIELD_MAGENTO_ORDER_ID,
            [
                'eq' => $oderId,
            ]
        );
        $orders = $collection->getItems();
        $order = reset($orders);

        return $order ?: null;
    }

    public function findByCheck24OrderId(string $oderId): ?OrderMappingInterface
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(
            OrderMappingInterface::FIELD_CHECK24_ORDER_ID,
            [
                'eq' => $oderId,
            ]
        );
        $mappingOrders = $collection->getItems();

        $mappingOrder = reset($mappingOrders);

        return $mappingOrder ?: null;
    }
}
