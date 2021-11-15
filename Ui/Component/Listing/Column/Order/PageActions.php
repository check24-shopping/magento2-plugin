<?php

namespace Check24Shopping\OrderImport\Ui\Component\Listing\Column\Order;

use Magento\Ui\Component\Listing\Columns\Column;

class PageActions extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $name = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item['error_message'] != '') {
                    $item['action'] = sprintf('<a href="%s">%s</a>',
                        $this->getContext()->getUrl('check24_orderimport/orderimport/retry', ['id' => $item['id']]),
                        __('Retry')
                    );
                } else {
                    $item['action'] = '';
                }
            }
        }

        return $dataSource;
    }

}
