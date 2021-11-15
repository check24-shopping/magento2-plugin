<?php

namespace Check24Shopping\OrderImport\Model;

use Check24Shopping\OrderImport\Api\Check24ShipmentRepositoryInterface;
use Check24Shopping\OrderImport\Api\Data\Check24ShipmentInterface;
use Check24Shopping\OrderImport\Api\Data\Check24ShipmentInterfaceFactory;
use Check24Shopping\OrderImport\Api\Data\OrderImportSearchResultsInterface;
use Check24Shopping\OrderImport\Api\Data\OrderImportSearchResultsInterfaceFactory;
use Check24Shopping\OrderImport\Model\ResourceModel\Check24Shipment\CollectionFactory as Check24ShipmentCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class Check24ShipmentRepository implements Check24ShipmentRepositoryInterface
{
    /** @var Check24ShipmentCollectionFactory */
    private $check24ShipmentCollectionFactory;
    /** @var OrderImportSearchResultsInterfaceFactory */
    private $orderSearchResultsFactory;
    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;
    /** @var DateTimeFactory */
    private $dateTimeFactory;
    /** @var Check24ShipmentInterfaceFactory */
    private $shipmentFactory;

    public function __construct(
        Check24ShipmentInterfaceFactory          $shipmentFactory,
        Check24ShipmentCollectionFactory         $Check24ShipmentCollectionFactory,
        OrderImportSearchResultsInterfaceFactory $orderSearchResultsFactory,
        SearchCriteriaBuilder                    $searchCriteriaBuilder,
        DateTimeFactory                          $dateTimeFactory
    )
    {
        $this->check24ShipmentCollectionFactory = $Check24ShipmentCollectionFactory;
        $this->orderSearchResultsFactory = $orderSearchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->shipmentFactory = $shipmentFactory;
    }

    public function save(Check24ShipmentInterface $shipment)
    {
        if ($shipment->isObjectNew()) {
            $shipment->setCreatedAt($this->dateTimeFactory->create()->gmtDate());
        }

        return $this->load($shipment->getId())
            ->setData($shipment->getData())
            ->save();
    }

    public function load($id): ?Check24ShipmentInterface
    {
        return $this->shipmentFactory
            ->create()
            ->load($id);
    }

    public function delete(Check24ShipmentInterface $shipment)
    {
        return $this
            ->load($shipment->getId())
            ->delete();
    }

    /**
     * @return OrderImportSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResult = $this->orderSearchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);

        $collection = $this->check24ShipmentCollectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setItems($collection->getItems());

        return $searchResult;
    }
}
