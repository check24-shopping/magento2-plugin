<?php

namespace Check24Shopping\OrderImport\Api;

use Check24Shopping\OrderImport\Api\Data\DynamicConfigInterface;

interface DynamicConfigRepositoryInterface
{
    public function save(DynamicConfigInterface $dynamicConfig);

    public function load(): DynamicConfigInterface;
}
