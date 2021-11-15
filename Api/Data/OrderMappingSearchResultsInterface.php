<?php

namespace Check24\OrderImport\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OrderMappingSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return OrderMappingInterface[]
     */
    public function getItems();

    /**
     * @param OrderMappingInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
