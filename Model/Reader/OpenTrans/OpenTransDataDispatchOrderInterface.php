<?php

namespace Check24Shopping\OrderImport\Model\Reader\OpenTrans;

interface OpenTransDataDispatchOrderInterface extends OpenTransDataOrderInterface
{
    public function getTrackNumber(): string;

    public function getDelivererParty(): OpenTransDataPartyInterface;
}
