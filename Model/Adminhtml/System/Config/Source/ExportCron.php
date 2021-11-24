<?php

namespace Check24Shopping\OrderImport\Model\Adminhtml\System\Config\Source;

use Check24Shopping\OrderImport\Helper\ExportConfig;

class ExportCron
{
    private $currentSetting;

    public function __construct(ExportConfig $exportConfig)
    {
        $this->currentSetting = $exportConfig->getCronSchedule();
    }

    public function toOptionArray(): array
    {
        return Cron::toOptionArray($this->currentSetting ?: '');
    }
}
