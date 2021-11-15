<?php

namespace Check24Shopping\OrderImport\Model\Writer\OpenTrans;

final class OpenTransOrderResponse extends OpenTransResponse
{
    protected function getType(): string
    {
        return 'ORDERRESPONSE';
    }

    protected function getItemName(): string
    {
        return 'ORDERRESPONSE_ITEM';
    }
}
