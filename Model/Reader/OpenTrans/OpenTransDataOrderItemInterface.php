<?php

namespace Check24\OrderImport\Model\Reader\OpenTrans;

interface OpenTransDataOrderItemInterface
{
    public function getSku(): string;

    public function getQuantity(): int;

    public function getPrice(): float;

    public function getDescriptionShort(): string;

    public function getId(): string;

    public function getUnit(): string;
}
