<?php

namespace Check24Shopping\OrderImport\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Export extends Container
{

    protected function _construct()
    {
        $this->_controller = 'adminhtml_export';
        $this->_blockGroup = 'Check24_orderimportProductExport';
        $this->_headerText = __('CHECK24 Product Exports');

        parent::_construct();

        /* Update default add button to Export All */
        $this->buttonList->update('add', 'label', __('Export All'));
        $this->buttonList->update('add', 'onclick', "setLocation('" . $this->getUrl('*/*/product') . "')");
    }
}
