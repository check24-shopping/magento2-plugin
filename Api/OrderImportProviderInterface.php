<?php

namespace Check24\OrderImport\Api;

use Check24\OrderImport\Api\Data\OrderImportInterface;
use Check24\OrderImport\Api\Data\OrderImportSearchResultsInterface;

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
