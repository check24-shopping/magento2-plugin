<?php

namespace Check24\OrderImport\Model;

use Check24\OrderImport\Api\Data\OrderImportInterface;
use Check24\OrderImport\Api\Data\OrderImportInterfaceFactory;
use Check24\OrderImport\Api\Data\OrderImportSearchResultsInterface;
use Check24\OrderImport\Api\Data\OrderImportSearchResultsInterfaceFactory;
use Check24\OrderImport\Api\OrderImportRepositoryInterface;
use Check24\OrderImport\Model\ResourceModel\OrderImport\CollectionFactory as OrderCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class OrderImportRepository implements OrderImportRepositoryInterface
{
    /** @var OrderImportInterfaceFactory */
    private $orderFactory;
    /** @var OrderCollectionFactory */
    private $orderCollectionFactory;
    /** @var OrderImportSearchResultsInterfaceFactory */
    private $orderSearchResultsFactory;
    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;
    /** @var DateTimeFactory */
    private $dateTimeFactory;

    public function __construct(
        OrderImportInterfaceFactory              $orderFactory,
        OrderCollectionFactory                   $orderCollectionFactory,
        OrderImportSearchResultsInterfaceFactory $orderSearchResultsFactory,
        SearchCriteriaBuilder                    $searchCriteriaBuilder,
        DateTimeFactory                          $dateTimeFactory
    )
    {
        $this->orderFactory = $orderFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderSearchResultsFactory = $orderSearchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dateTimeFactory = $dateTimeFactory;
    }

    public function save(OrderImportInterface $shipment)
    {
        if ($shipment->isObjectNew()) {
            $shipment->setCreatedAt($this->dateTimeFactory->create()->gmtDate());
        } else {
            $shipment->setUpdatedAt($this->dateTimeFactory->create()->gmtDate());
        }

        return $this->load($shipment->getId())
            ->setData($shipment->getData())
            ->save();
    }

    public function load($id): ?OrderImportInterface
    {
        return $this->orderFactory
            ->create()
            ->load($id);
    }

    public function delete(OrderImportInterface $order)
    {
        return $this
            ->load($order->getId())
            ->delete();
    }

    /**
     * @return OrderImportSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResult = $this->orderSearchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);

        $collection = $this->orderCollectionFactory->create();

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
