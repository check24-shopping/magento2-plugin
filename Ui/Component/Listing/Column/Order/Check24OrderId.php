<?php

namespace Check24Shopping\OrderImport\Ui\Component\Listing\Column\Order;

use Check24Shopping\OrderImport\Api\Data\OrderImportInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Check24OrderId extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[OrderImportInterface::FIELD_CHECK24_ORDER_ID] = sprintf('<a href="%s" target="_blank">%s</a>', $this->getContext()->getUrl('check24shopping_orderimport/orderimport/view', ['id' => $item['id']]), $item['check24shopping_order_id']);
            }
        }

        return $dataSource;
    }

}
