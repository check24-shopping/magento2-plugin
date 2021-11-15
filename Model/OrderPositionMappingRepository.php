<?php

namespace Check24Shopping\OrderImport\Model;

use Check24Shopping\OrderImport\Api\Data\OrderPositionMappingInterface;
use Check24Shopping\OrderImport\Api\Data\OrderPositionMappingInterfaceFactory;
use Check24Shopping\OrderImport\Api\OrderPositionMappingRepositoryInterface;
use Check24Shopping\OrderImport\Model\ResourceModel\OrderPositionMapping\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class OrderPositionMappingRepository implements OrderPositionMappingRepositoryInterface
{
    /** @var OrderPositionMappingInterfaceFactory */
    private $orderFactory;
    /** @var DateTimeFactory */
    private $dateTimeFactory;
    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        OrderPositionMappingInterfaceFactory $orderFactory,
        SearchCriteriaBuilder                $searchCriteriaBuilder,
        DateTimeFactory                      $dateTimeFactory,
        CollectionFactory                    $collectionFactory
    )
    {
        $this->orderFactory = $orderFactory;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionFactory = $collectionFactory;
    }

    public function save(OrderPositionMappingInterface $orderPositionMapping)
    {
        return $this->load($orderPositionMapping->getId())
            ->setData($orderPositionMapping->getData())
            ->save();
    }

    public function load($id): ?OrderPositionMappingInterface
    {
        return $this->orderFactory
            ->create()
            ->load($id);
    }

    public function delete(OrderPositionMappingInterface $orderPositionMapping)
    {
        return $this
            ->load($orderPositionMapping->getId())
            ->delete();
    }

    public function findByMagentoPositionId(string $magentoPositionId): ?OrderPositionMapping
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(OrderPositionMappingInterface::FIELD_MAGENTO_POSITION_ID, $magentoPositionId)
            ->create();

        $collection = $this->collectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $mappingItem = $collection->getItems();
        $mappingItem = reset($mappingItem);

        return $mappingItem ?: null;
    }
}
