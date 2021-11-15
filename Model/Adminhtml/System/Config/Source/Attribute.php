<?php

namespace Check24Shopping\OrderImport\Model\Adminhtml\System\Config\Source;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;

class Attribute implements ArrayInterface
{

    /**
     * @var CollectionFactory
     */
    private $_collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    )
    {
        $this->_collectionFactory = $collectionFactory;
    }

    public function getAllOptions(): array
    {
        return $this->toOptionArray();
    }

    public function toOptionArray(): array
    {
        $attributes = $this->getAttributes();
        array_unshift($attributes, array('value' => '', 'label' => __('-- Please select --')));

        return $attributes;
    }

    public function getAttributes(): array
    {

        $collection = $this->_collectionFactory->create();

        $attr_groups = array();

        foreach ($collection as $item) {
            $attr_groups[] = [
                'value' => $item->getData()['attribute_code'],
                'label' => $item->getData()['frontend_label'] . ' [' . $item->getData()['attribute_code'] . ']'
            ];
        }

        return $attr_groups;
    }
}
