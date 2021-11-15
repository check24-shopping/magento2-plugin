<?php

namespace Check24Shopping\OrderImport\Ui\Component\Listing\Column\Order;

use Magento\Backend\Block\Widget\Context;
use Magento\Customer\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ProcessButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context  $context,
        Registry $registry
    )
    {
        parent::__construct($context, $registry);
    }

    public function getButtonData()
    {

        $data = [
            'label' => __('Process Orders'),
            'class' => 'primary',
            'on_click' => sprintf("location.href = '%s';", $this->getProcessUrl()),
            'sort_order' => 50,
        ];

        return $data;
    }

    /**
     * URL getter
     *
     * @return string
     */
    public function getProcessUrl()
    {
        return $this->getUrl('check24_orderimport/orderimport/process');
    }
}
