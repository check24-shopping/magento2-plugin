<?php

namespace Check24Shopping\OrderImport\Model\ValueObject\Interfaces;

interface ErrorMessageInterface
{
    public function getOrderNumber(): string;

    public function getDocumentNumber(): string;

    public function getErrorMessage(): string;
}
