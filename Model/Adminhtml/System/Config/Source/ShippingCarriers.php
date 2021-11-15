<?php

namespace Check24Shopping\OrderImport\Model\Adminhtml\System\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Config;
use Magento\Store\Model\ScopeInterface;

class ShippingCarriers
{
    protected $scopeConfig;

    protected $shipConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config               $shipConfig
    )
    {
        $this->shipConfig = $shipConfig;
        $this->scopeConfig = $scopeConfig;
    }

    public function toOptionArray()
    {
        $activeCarriers = $this->shipConfig->getActiveCarriers();

        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            $carrierMethods = $carrierModel->getAllowedMethods();
            if (!$carrierMethods) {
                continue;
            }
            $carrierTitle = $this->scopeConfig->getValue(
                'carriers/' . $carrierCode . '/title',
                ScopeInterface::SCOPE_STORE
            );
            $methods[$carrierCode] = ['label' => $carrierTitle, 'value' => []];
            foreach ($carrierMethods as $methodCode => $methodTitle) {
                $methods[$carrierCode]['value'][] = [
                    'value' => $carrierCode . '_' . $methodCode,
                    'label' => '[' . $carrierCode . '] ' . $methodTitle,
                ];
            }
        }

        return $methods;
    }
}
