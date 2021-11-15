<?php

namespace Check24\OrderImport\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OrderImportSearchResultsInterface extends SearchResultsInterface
{

    /**
     * @return OrderImportInterface[]
     */
    public function getItems();

    /**
     * @param OrderImportInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
