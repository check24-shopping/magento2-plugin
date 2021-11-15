<?php

namespace Check24\OrderImport\Model\Reader\OpenTrans;

interface OpenTransDocumentInterface
{
    const ACTION_RETURN_REQUEST = 'returnrequest';
    const ACTION_CANCELLATION_REQUEST = 'cancellationrequest';

    public function getDocumentId(): string;

    public function getType(): string;

    public function getOrderId(): string;

    public function getContent(): string;

    public function getAction(): string;
}
