<?php

namespace Check24Shopping\OrderImport\Api;

use Check24Shopping\OrderImport\Api\Data\Check24CancelInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface Check24CancelRepositoryInterface
{
    public function save(Check24CancelInterface $cancel);

    public function load($id): ?Check24CancelInterface;

    public function delete(Check24CancelInterface $cancel);

    public function getList(SearchCriteriaInterface $searchCriteria);
}
