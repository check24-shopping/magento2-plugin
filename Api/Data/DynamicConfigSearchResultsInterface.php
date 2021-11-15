<?php

namespace Check24\OrderImport\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface DynamicConfigSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return DocumentTrackingInterface[]
     */
    public function getItems();

    /**
     * @param DocumentTrackingInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
