<?php

namespace Check24Shopping\OrderImport\Api;

use Check24Shopping\OrderImport\Api\Data\ReturnRequestInterface;
use Check24Shopping\OrderImport\Model\ResourceModel\ReturnRequest\Collection;
use Magento\Framework\Api\SearchCriteriaInterface;

interface ReturnRequestRepositoryInterface
{
    public function save(ReturnRequestInterface $ReturnRequest);

    public function load($id): ?ReturnRequestInterface;

    public function delete(ReturnRequestInterface $order);

    public function getList(SearchCriteriaInterface $searchCriteria): array;
}
