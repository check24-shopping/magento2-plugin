<?php

namespace Check24Shopping\OrderImport\Block\Adminhtml\System\Config;

use Check24Shopping\OrderImport\Block\Adminhtml\Form\Field\Attributes;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class Priceshipping extends AbstractFieldArray
{
    /**
     * @var $_attributesRenderer Attributes
     */
    protected $_attributes;

    /**
     * Prepare to render.
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'mapped_attribute',
            [
                'label' => __('Price Shipping Attribute'),
                'renderer' => $this->_getAttributeRenderer()
            ]
        );

        $this->addColumn('defaultvalue', ['label' => __('Default Price Shipping')]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Attribute');
    }

    /**
     * Get activation options.
     *
     * @return Attributes
     */
    protected function _getAttributeRenderer()
    {
        if (!$this->_attributes) {
            $this->_attributes = $this->getLayout()->createBlock(
                '\Check24Shopping\OrderImport\Block\Adminhtml\Form\Field\Attributes',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }

        return $this->_attributes;
    }

    /**
     * Prepare existing row data object.
     *
     * @param DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $options = [];
        $customAttribute = $row->getData('mapped_attribute');

        $key = 'option_' . $this->_getAttributeRenderer()->calcOptionHash($customAttribute);
        $options[$key] = 'selected="selected"';
        $row->setData('option_extra_attrs', $options);
    }
}
