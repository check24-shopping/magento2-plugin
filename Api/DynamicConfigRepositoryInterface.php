<?php

namespace Check24\OrderImport\Api;

use Check24\OrderImport\Api\Data\DynamicConfigInterface;

interface DynamicConfigRepositoryInterface
{
    public function save(DynamicConfigInterface $dynamicConfig);

    public function load(): DynamicConfigInterface;
}
