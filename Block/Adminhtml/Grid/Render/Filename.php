<?php

namespace Check24Shopping\OrderImport\Block\Adminhtml\Grid\Render;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Store render website
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Filename extends AbstractRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(DataObject $row)
    {
        return '<a title="' . __('Download CSV') . '" href="' .
            $this->escapeHtml($row->getData($this->getColumn()->getIndex())) . '" target="_blank">' .
            $this->escapeHtml($row->getData($this->getColumn()->getIndex())) . '</a>';
    }
}
