<?php

namespace Check24Shopping\OrderImport\Model\Adminhtml\System\Config\Source;

use Check24Shopping\OrderImport\Helper\Config\OrderConfig;

class ImportCron
{
    private $currentSetting;

    public function __construct(OrderConfig $orderConfig)
    {
        $this->currentSetting = $orderConfig->getCronSchedule();
    }

    public function toOptionArray(): array
    {
        return Cron::toOptionArray($this->currentSetting);
    }
}
