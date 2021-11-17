<?php

namespace Check24Shopping\OrderImport\Model\Adminhtml\System\Config\Source;

class Cron
{
    public static function toOptionArray(string $currentSetting): array
    {
        $schedules = [['value' => '', 'label' => __('Please select')]];
        $intervals = [
            5 => 'Every 5 minutes',
            15 => 'Every 15 minutes',
            30 => 'Every 30 minutes',
            60 => 'Once an hour',
        ];
        $intervalCount = substr_count($currentSetting, ',') + 1;
        foreach ($intervals as $interval => $label) {
            $schedules[] =
                60 / $interval === $intervalCount
                    ? ['value' => $currentSetting, 'label' => $label]
                    : ['value' => self::createCronMinutes($interval), 'label' => $label];
        }

        return $schedules;
    }

    private static function createCronMinutes($interval): string
    {
        $random = rand(0, $interval - 1);
        $minutes = [];
        for ($loops = 0; $loops < 60 / $interval; $loops++) {
            $minutes[] = $loops * $interval + $random;
        }

        return implode(',', $minutes) . ' * * * *';
    }
}
