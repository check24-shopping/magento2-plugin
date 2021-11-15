<?php

namespace Check24\OrderImport\Model\Writer\OpenTrans;

final class OpenTransCancelResponse extends OpenTransResponse
{
    protected function getType(): string
    {
        return 'ORDERCHANGE';
    }

    protected function getItemName(): string
    {
        return 'ORDER_ITEM';
    }
}
