<?php

namespace Check24Shopping\OrderImport\Model;

use Check24Shopping\OrderImport\Api\Data\ReturnRequestInterface;
use Check24Shopping\OrderImport\Api\Data\ReturnRequestInterfaceFactory;
use Check24Shopping\OrderImport\Api\ReturnRequestRepositoryInterface;
use Check24Shopping\OrderImport\Api\Data\OrderImportSearchResultsInterfaceFactory;
use Check24Shopping\OrderImport\Model\ResourceModel\ReturnRequest\Collection;
use Check24Shopping\OrderImport\Model\ResourceModel\ReturnRequest\CollectionFactory as ReturnRequestCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class ReturnRequestRepository implements ReturnRequestRepositoryInterface
{
    /** @var ReturnRequestInterfaceFactory */
    private $returnRequestFactory;
    /** @var DateTimeFactory */
    private $dateTimeFactory;
    /**
     * @var OrderImportSearchResultsInterfaceFactory
     */
    private $requestSearchResultsInterfaceFactory;
    /**
     * @var ReturnRequestCollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        ReturnRequestInterfaceFactory $returnRequestFactory,
        DateTimeFactory               $dateTimeFactory,
        OrderImportSearchResultsInterfaceFactory $requestSearchResultsInterfaceFactory,
        ReturnRequestCollectionFactory $collectionFactory
    )
    {
        $this->returnRequestFactory = $returnRequestFactory;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->requestSearchResultsInterfaceFactory = $requestSearchResultsInterfaceFactory;
        $this->collectionFactory = $collectionFactory;
    }

    public function save(ReturnRequestInterface $ReturnRequest)
    {
        if ($ReturnRequest->isObjectNew()) {
            $ReturnRequest->setCreatedAt($this->dateTimeFactory->create()->gmtDate());
        }

        return $this->load($ReturnRequest->getId())
            ->setData($ReturnRequest->getData())
            ->save();
    }

    public function load($id): ?ReturnRequestInterface
    {
        return $this->returnRequestFactory
            ->create()
            ->load($id);
    }

    public function delete(ReturnRequestInterface $ReturnRequest)
    {
        return $this
            ->load($ReturnRequest->getId())
            ->delete();
    }

    public function getList(SearchCriteriaInterface $searchCriteria): array
    {
        $searchResult = $this->requestSearchResultsInterfaceFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);

        $collection = $this->collectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        return $collection->getItems();
    }
}
