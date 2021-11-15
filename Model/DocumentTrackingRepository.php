<?php

namespace Check24\OrderImport\Model;

use Check24\OrderImport\Api\Data\DocumentTrackingInterface;
use Check24\OrderImport\Api\Data\DocumentTrackingInterfaceFactory;
use Check24\OrderImport\Api\DocumentTrackingRepositoryInterface;
use Check24\OrderImport\Model\ResourceModel\DocumentTracking\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;

class DocumentTrackingRepository implements DocumentTrackingRepositoryInterface
{
    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;
    /** @var CollectionFactory */
    private $collectionFactory;
    /** @var DocumentTrackingInterfaceFactory */
    private $documentTrackingFactory;

    public function __construct(
        DocumentTrackingInterfaceFactory $documentTrackingFactory,
        SearchCriteriaBuilder            $searchCriteriaBuilder,
        CollectionFactory                $collectionFactory
    )
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionFactory = $collectionFactory;
        $this->documentTrackingFactory = $documentTrackingFactory;
    }

    public function save(DocumentTrackingInterface $documentTracking)
    {
        return $this->load($documentTracking->getId())
            ->setData($documentTracking->getData())
            ->save();
    }

    public function load($id): ?DocumentTrackingInterface
    {
        return $this
            ->documentTrackingFactory
            ->create()
            ->load($id);
    }

    public function delete(DocumentTrackingInterface $documentTracking)
    {
        return $this
            ->load($documentTracking->getId())
            ->delete();
    }

    public function findByDocumentId(int $documentId): ?DocumentTrackingInterface
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(DocumentTrackingInterface::FIELD_DOCUMENT_ID, $documentId)
            ->create();

        $collection = $this->collectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $documentTracking = $collection->getItems();
        $documentTracking = reset($documentTracking);

        return $documentTracking ?: null;
    }
}
