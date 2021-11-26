<?php

namespace Check24Shopping\OrderImport\Ui\Component\Listing\Column\Order;

use Magento\Ui\Component\Listing\Columns\Column;

class Error extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $errorMessage = $item['error_message'] ?? null;
                if ($errorMessage) {
                    $item['error'] = '<a href="' .
                        $this
                            ->getContext()
                            ->getUrl('check24shopping_orderimport/orderimport/errorview', ['id' => $item['id']]) .
                        '" target="_blank">' . $errorMessage . '</a>';
                } else {
                    $item['error'] = '-';
                }
            }
        }

        return $dataSource;
    }
}
