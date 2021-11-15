<?php

namespace Check24Shopping\OrderImport\Plugin;

use Check24Shopping\OrderImport\Api\Check24ReturnRepositoryInterface;
use Check24Shopping\OrderImport\Api\DynamicConfigRepositoryInterface;
use Check24Shopping\OrderImport\Api\OrderMappingRepositoryInterface;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Block\Adminhtml\Order\View;

class PluginBtnOrderView
{
    /** @var ObjectManagerInterface */
    protected $object_manager;
    /** @var UrlInterface */
    protected $_backendUrl;
    /** @var OrderMappingRepositoryInterface */
    private $orderMappingRepository;
    /**
     * @var Check24ReturnRepositoryInterface
     */
    private $returnRepository;
    /**
     * @var DynamicConfigRepositoryInterface
     */
    private $dynamicConfigRepository;

    public function __construct(
        ObjectManagerInterface           $om,
        UrlInterface                     $backendUrl,
        OrderMappingRepositoryInterface  $orderMappingRepository,
        Check24ReturnRepositoryInterface $returnRepository,
        DynamicConfigRepositoryInterface $dynamicConfigRepository
    )
    {
        $this->object_manager = $om;
        $this->_backendUrl = $backendUrl;
        $this->orderMappingRepository = $orderMappingRepository;
        $this->returnRepository = $returnRepository;
        $this->dynamicConfigRepository = $dynamicConfigRepository;
    }

    public function beforeSetLayout(View $subject)
    {
        if ($this->dynamicConfigRepository->load()->getSendReturn() === false) {
            return;
        }
        $mappingOrder = $this->orderMappingRepository->findByOrderId($subject->getOrderId());
        if (empty($mappingOrder)) {
            return;
        }
        $return = $this->returnRepository->findByOrderId($subject->getOrderId());
        if (empty($return) === false) {
            return;
        }
        $sendOrder = $this->_backendUrl->getUrl('check24shopping_orderimport/orderimport/addreturn/', ['orderId' => $subject->getOrderId()]);#/order_id/' . $subject->getOrderId()
        $subject->addButton(
            'check24return',
            [
                'label' => __('Rücksendung an Check24 melden'),
                'onclick' => "setLocation('" . $sendOrder . "')",
                'class' => __('custom-button'),
            ]
        );
    }
}
