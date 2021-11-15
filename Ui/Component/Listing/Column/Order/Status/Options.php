<?php

namespace Check24Shopping\OrderImport\Ui\Component\Listing\Column\Order\Status;

use Magento\Framework\Data\OptionSourceInterface;

class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [
                ['value' => 0, 'label' => __('Imported')],
                ['value' => 1, 'label' => __('Processed')],
                ['value' => 2, 'label' => __('Responded')],
            ];
        }

        return $this->options;
    }
}
