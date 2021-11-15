<?php

namespace Check24\OrderImport\Model\ValueObject\Interfaces;

interface ErrorMessageInterface
{
    public function getOrderNumber(): string;

    public function getDocumentNumber(): string;

    public function getErrorMessage(): string;
}
