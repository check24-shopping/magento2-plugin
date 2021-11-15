<?php

namespace Check24Shopping\OrderImport\Api;

use Check24Shopping\OrderImport\Api\Data\OrderPositionMappingInterface;
use Check24Shopping\OrderImport\Model\OrderPositionMapping;

interface OrderPositionMappingRepositoryInterface
{
    public function save(OrderPositionMappingInterface $orderPositionMapping);

    public function load($id): ?OrderPositionMappingInterface;

    public function delete(OrderPositionMappingInterface $orderPositionMapping);

    public function findByMagentoPositionId(string $magentoPositionId): ?OrderPositionMapping;
}
