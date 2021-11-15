<?php

namespace Check24Shopping\OrderImport\Model\Adminhtml\System\Config\Source;

use Magento\Catalog\Model\Product\AttributeSet\Options;

class AttributeSets
{
    const ATTRIBUTE_SETS_ALL = 'all';
    private $_attributesets;

    public function __construct(
        Options $options
    )
    {
        $this->_attributesets = $options;
    }

    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        $attributes = $this->_attributesets->toOptionArray();
        array_unshift($attributes, array('value' => self::ATTRIBUTE_SETS_ALL, 'label' => __('All')));

        return $attributes;
    }
}
