<?php

namespace Check24Shopping\OrderImport\Api;

use Check24Shopping\OrderImport\Api\Data\OrderImportInterface;
use Check24Shopping\OrderImport\Api\Data\OrderImportSearchResultsInterface;

interface OrderImportProviderInterface
{
    /**
     * @return OrderImportSearchResultsInterface
     */
    public function getImportedList();

    /**
     * @return OrderImportSearchResultsInterface
     */
    public function getCancelList();

    /**
     * @return OrderImportSearchResultsInterface
     */
    public function getReturnRequestList();

    /**
     * @return OrderImportSearchResultsInterface
     */
    public function getNotRespondedList();

    public function getByOrderNumber(string $orderNumber): ?OrderImportInterface;
}
