<?php

namespace Check24\OrderImport\Ui\Component\Listing\Column\Order;

use Magento\Ui\Component\Listing\Columns\Column;

class Error extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $errorMessage = $item['error_message'] ?? null;
                $item['error'] = $errorMessage ? $errorMessage : '-';
            }
        }

        return $dataSource;
    }
}
