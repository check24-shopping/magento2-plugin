<?php

namespace Check24Shopping\OrderImport\Block\Adminhtml\Form\Field;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Config\Model\Config\Source\Enabledisable;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class Attributes extends Select
{
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Activation constructor.
     *
     * @param Context $context
     * @param Enabledisable $enableDisable $enableDisable
     * @param array $data
     */
    public function __construct(
        Context           $context,
        CollectionFactory $collectionFactory,
        array             $data = []
    )
    {
        parent::__construct($context, $data);

        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * @param string $value
     * @return Magently\Tutorial\Block\Adminhtml\Form\Field\Activation
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Parse to html.
     *
     * @return mixed
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $collection = $this->_collectionFactory->create();

            $this->addOption('', __('-- Not defined --'));
            foreach ($collection as $item) {
                $this->addOption(
                    $item->getData()['attribute_code'],
                    $item->getData()['frontend_label'] . ' [' . $item->getData()['attribute_code'] . ']'
                );
            }

        }

        return parent::_toHtml();
    }
}
