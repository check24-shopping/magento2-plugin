<?php

namespace Check24Shopping\OrderImport\Model\Reader\OpenTrans\Entity;

use Check24Shopping\OrderImport\Model\Reader\OpenTrans\OpenTransDataAddressInterface;
use Check24Shopping\OrderImport\Model\Reader\OpenTrans\OpenTransDataPartyInterface;

final class OpenTransSupplierPartyEntity implements OpenTransDataPartyInterface
{
    /** @var string */
    private $id;
    /** @var OpenTransDataAddressInterface */
    private $address;

    public function __construct(
        string                        $id,
        OpenTransDataAddressInterface $address
    )
    {
        $this->id = $id;
        $this->address = $address;
    }

    public function getRole(): string
    {
        return 'supplier';
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAddress(): OpenTransDataAddressInterface
    {
        return $this->address;
    }

    public function getIdType(): string
    {
        return 'check24';
    }
}
