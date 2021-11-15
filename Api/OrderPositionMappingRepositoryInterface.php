<?php

namespace Check24\OrderImport\Api;

use Check24\OrderImport\Api\Data\OrderPositionMappingInterface;
use Check24\OrderImport\Model\OrderPositionMapping;

interface OrderPositionMappingRepositoryInterface
{
    public function save(OrderPositionMappingInterface $orderPositionMapping);

    public function load($id): ?OrderPositionMappingInterface;

    public function delete(OrderPositionMappingInterface $orderPositionMapping);

    public function findByMagentoPositionId(string $magentoPositionId): ?OrderPositionMapping;
}
