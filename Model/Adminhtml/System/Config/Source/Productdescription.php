<?php

namespace Check24Shopping\OrderImport\Model\Adminhtml\System\Config\Source;

class Productdescription
{

    public function toOptionArray()
    {
        return array(
            array('value' => 'short_description', 'label' => __('Short Description')),
            array('value' => 'description', 'label' => __('Description'))
        );
    }
}
