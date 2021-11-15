<?php

namespace Check24Shopping\OrderImport\Model;

use Check24Shopping\OrderImport\Api\Check24ReturnRepositoryInterface;
use Check24Shopping\OrderImport\Api\Data\Check24ReturnInterface;
use Check24Shopping\OrderImport\Api\Data\Check24ReturnInterfaceFactory;
use Check24Shopping\OrderImport\Api\Data\OrderImportSearchResultsInterface;
use Check24Shopping\OrderImport\Api\Data\OrderImportSearchResultsInterfaceFactory;
use Check24Shopping\OrderImport\Model\ResourceModel\Check24Return\CollectionFactory as Check24ReturnCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class Check24ReturnRepository implements Check24ReturnRepositoryInterface
{
    /** @var Check24ReturnCollectionFactory */
    private $check24ReturnCollectionFactory;
    /** @var OrderImportSearchResultsInterfaceFactory */
    private $orderSearchResultsFactory;
    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;
    /** @var DateTimeFactory */
    private $dateTimeFactory;
    /** @var Check24ReturnInterfaceFactory */
    private $ReturnFactory;

    public function __construct(
        Check24ReturnInterfaceFactory            $ReturnFactory,
        Check24ReturnCollectionFactory           $Check24ReturnCollectionFactory,
        OrderImportSearchResultsInterfaceFactory $orderSearchResultsFactory,
        SearchCriteriaBuilder                    $searchCriteriaBuilder,
        DateTimeFactory                          $dateTimeFactory
    )
    {
        $this->check24ReturnCollectionFactory = $Check24ReturnCollectionFactory;
        $this->orderSearchResultsFactory = $orderSearchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->ReturnFactory = $ReturnFactory;
    }

    public function save(Check24ReturnInterface $return)
    {
        if ($return->isObjectNew()) {
            $return->setCreatedAt($this->dateTimeFactory->create()->gmtDate());
        }

        return $this->load($return->getId())
            ->setData($return->getData())
            ->save();
    }

    public function load($id): ?Check24ReturnInterface
    {
        return $this->ReturnFactory
            ->create()
            ->load($id);
    }

    public function delete(Check24ReturnInterface $return)
    {
        return $this
            ->load($return->getId())
            ->delete();
    }

    /**
     * @return OrderImportSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResult = $this->orderSearchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);

        $collection = $this->check24ReturnCollectionFactory->create();

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

    public function findByOrderId(string $oderId): ?Check24ReturnInterface
    {
        $collection = $this->check24ReturnCollectionFactory->create();
        $collection->addFieldToFilter(
            Check24ReturnInterface::FIELD_ORDER_ID,
            [
                'eq' => $oderId,
            ]
        );
        $returns = $collection->getItems();
        $return = reset($returns);

        return $return ?: null;
    }
}
