<?php

namespace Check24\OrderImport\Model\Adminhtml\System\Config\Source;

use Check24\OrderImport\Helper\Config\OrderConfig;

class Cron
{
    private $currentSetting;

    public function __construct(
        OrderConfig $orderConfig
    )
    {
        $this->currentSetting = $orderConfig->getCronSchedule();
    }

    public function toOptionArray(): array
    {
        $schedules = [['value' => '', 'label' => __('Please select')]];
        $intervals = [
            5 => 'Every 5 minutes',
            15 => 'Every 15 minutes',
            30 => 'Every 30 minutes',
            60 => 'Once an hour',
        ];
        $intervalCount = substr_count($this->currentSetting, ',') + 1;
        foreach ($intervals as $interval => $label) {
            $schedules[] =
                60 / $interval === $intervalCount
                    ? ['value' => $this->currentSetting, 'label' => $label]
                    : ['value' => $this->createCronMinutes($interval), 'label' => $label];
        }

        return $schedules;
    }

    private function createCronMinutes($interval): string
    {
        $random = rand(0, $interval - 1);
        $minutes = [];
        for ($loops = 0; $loops < 60 / $interval; $loops++) {
            $minutes[] = $loops * $interval + $random;
        }

        return implode(',', $minutes) . ' * * * *';
    }
}
