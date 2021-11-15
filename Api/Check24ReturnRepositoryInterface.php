<?php

namespace Check24Shopping\OrderImport\Api;

use Check24Shopping\OrderImport\Api\Data\Check24ReturnInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface Check24ReturnRepositoryInterface
{
    public function save(Check24ReturnInterface $return);

    public function load($id): ?Check24ReturnInterface;

    public function delete(Check24ReturnInterface $return);

    public function getList(SearchCriteriaInterface $searchCriteria);

    public function findByOrderId(string $oderId): ?Check24ReturnInterface;
}
