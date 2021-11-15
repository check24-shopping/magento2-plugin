<?php

namespace Check24\OrderImport\Block\Adminhtml\Grid\Render;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class Action extends AbstractRenderer
{

    public function render(DataObject $row)
    {
        return '<a title="' . __(
                'Export'
            ) . '"
            href="' .
            $this->getUrl('check24_orderimport/export/product', ['id' => $row->getId()]) .
            '">' . __('Export') . '</a>';
    }
}
