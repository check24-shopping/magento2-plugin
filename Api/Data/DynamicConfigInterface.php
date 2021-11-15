<?php

namespace Check24\OrderImport\Api\Data;

interface DynamicConfigInterface
{
    const TABLE_NAME = 'check24_configuration';
    const FIELD_ID = 'id';
    const FIELD_PROCESS_CANCEL = 'process_cancel';
    const FIELD_SEND_CANCEL = 'send_cancel';
    const FIELD_SEND_DISPATCH = 'send_dispatch';
    const FIELD_SEND_RETURN = 'send_return';
    const FIELD_PROCESS_RETURN = 'process_return';

    public function setId($id): self;

    public function getId();

    public function setProcessCancel(bool $value): self;

    public function getProcessCancel(): bool;

    public function setSendCancel(bool $value): self;

    public function getSendCancel(): bool;

    public function setSendDispatch(bool $value): self;

    public function getSendDispatch(): bool;

    public function setSendReturn(bool $value): self;

    public function getSendReturn(): bool;

    public function setProcessReturn(bool $value): self;

    public function getProcessReturn(): bool;

}
