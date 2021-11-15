<?php

namespace Check24Shopping\OrderImport\Model;

use Check24Shopping\OrderImport\Api\Check24CancelRepositoryInterface;
use Check24Shopping\OrderImport\Api\Data\Check24CancelInterface;
use Check24Shopping\OrderImport\Api\Data\Check24CancelInterfaceFactory;
use Check24Shopping\OrderImport\Api\Data\OrderImportSearchResultsInterface;
use Check24Shopping\OrderImport\Api\Data\OrderImportSearchResultsInterfaceFactory;
use Check24Shopping\OrderImport\Model\ResourceModel\Check24Cancel\CollectionFactory as Check24CancelCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class Check24CancelRepository implements Check24CancelRepositoryInterface
{
    /** @var Check24CancelCollectionFactory */
    private $check24CancelCollectionFactory;
    /** @var OrderImportSearchResultsInterfaceFactory */
    private $orderSearchResultsFactory;
    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;
    /** @var DateTimeFactory */
    private $dateTimeFactory;
    /** @var Check24CancelInterfaceFactory */
    private $CancelFactory;

    public function __construct(
        Check24CancelInterfaceFactory            $CancelFactory,
        Check24CancelCollectionFactory           $Check24CancelCollectionFactory,
        OrderImportSearchResultsInterfaceFactory $orderSearchResultsFactory,
        SearchCriteriaBuilder                    $searchCriteriaBuilder,
        DateTimeFactory                          $dateTimeFactory
    )
    {
        $this->check24CancelCollectionFactory = $Check24CancelCollectionFactory;
        $this->orderSearchResultsFactory = $orderSearchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->CancelFactory = $CancelFactory;
    }

    public function save(Check24CancelInterface $cancel)
    {
        if ($cancel->isObjectNew()) {
            $cancel->setCreatedAt($this->dateTimeFactory->create()->gmtDate());
        }

        return $this->load($cancel->getId())
            ->setData($cancel->getData())
            ->save();
    }

    public function load($id): ?Check24CancelInterface
    {
        return $this->CancelFactory
            ->create()
            ->load($id);
    }

    public function delete(Check24CancelInterface $cancel)
    {
        return $this
            ->load($cancel->getId())
            ->delete();
    }

    /**
     * @return OrderImportSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResult = $this->orderSearchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);

        $collection = $this->check24CancelCollectionFactory->create();

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
