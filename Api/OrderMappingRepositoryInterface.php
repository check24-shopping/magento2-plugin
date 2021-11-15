<?php

namespace Check24\OrderImport\Api;

use Check24\OrderImport\Api\Data\OrderMappingInterface;

interface OrderMappingRepositoryInterface
{
    public function save(OrderMappingInterface $orderMapping);

    public function load($id): ?OrderMappingInterface;

    public function delete(OrderMappingInterface $order);

    public function findByCheck24OrderId(string $oderId): ?OrderMappingInterface;

    public function findByOrderId(string $oderId): ?OrderMappingInterface;
}
