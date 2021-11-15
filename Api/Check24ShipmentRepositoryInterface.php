<?php

namespace Check24\OrderImport\Api;

use Check24\OrderImport\Api\Data\Check24ShipmentInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface Check24ShipmentRepositoryInterface
{
    public function save(Check24ShipmentInterface $shipment);

    public function load($id): ?Check24ShipmentInterface;

    public function delete(Check24ShipmentInterface $shipment);

    public function getList(SearchCriteriaInterface $searchCriteria);
}
