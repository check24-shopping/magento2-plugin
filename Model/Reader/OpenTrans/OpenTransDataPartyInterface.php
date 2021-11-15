<?php

namespace Check24Shopping\OrderImport\Model\Reader\OpenTrans;

interface OpenTransDataPartyInterface
{
    public function getRole(): string;

    public function getId(): string;

    public function getAddress(): OpenTransDataAddressInterface;

    public function getIdType(): string;
}
