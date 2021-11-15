<?php

namespace Check24Shopping\OrderImport\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface OrderPositionMappingSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return OrderPositionMappingInterface[]
     */
    public function getItems();

    /**
     * @param OrderPositionMappingInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
