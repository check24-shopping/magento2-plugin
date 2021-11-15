<?php

namespace Check24Shopping\OrderImport\Api;

use Check24Shopping\OrderImport\Api\Data\OrderImportInterface;
use Check24Shopping\OrderImport\Api\Data\OrderImportSearchResultsInterface;

interface OrderImportProviderInterface
{
    public function getImportedList();

    public function getCancelList();

    /**
     * @return OrderImportSearchResultsInterface
     */
    public function getNotRespondedList();

    public function getByOrderNumber(string $orderNumber): ?OrderImportInterface;
}
