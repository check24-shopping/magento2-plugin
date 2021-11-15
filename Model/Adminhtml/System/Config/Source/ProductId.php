<?php

namespace Check24\OrderImport\Model\Adminhtml\System\Config\Source;

class ProductId
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'id', 'label' => __('Magento Product ID')],
            ['value' => 'sku', 'label' => __('SKU')]
        ];
    }
}
