<?php

namespace Check24\OrderImport\Api;

use Check24\OrderImport\Api\Data\OrderImportInterface;
use Check24\OrderImport\Api\Data\OrderImportSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface OrderImportRepositoryInterface
{
    public function save(OrderImportInterface $shipment);

    public function load($id): ?OrderImportInterface;

    public function delete(OrderImportInterface $order);

    /**
     * @return OrderImportSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
