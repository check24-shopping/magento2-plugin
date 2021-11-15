<?php


namespace Check24\OrderImport\Model\Reader\OpenTrans\Entity;


use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataAddressInterface;

final class OpenTransAddressEntity implements OpenTransDataAddressInterface
{
    public function getCompany(): string
    {
        return '';
    }

    public function getFirstname(): string
    {
        return '';
    }

    public function getLastname(): string
    {
        return '';
    }

    public function getStreet(): string
    {
        return '';
    }

    public function getZip(): string
    {
        return '';
    }

    public function getCity(): string
    {
        return '';
    }

    public function getCountry(): string
    {
        return '';
    }

    public function getCountryCode(): string
    {
        return '';
    }

    public function getPhone(): string
    {
        return '';
    }

    public function getEmail(): string
    {
        return '';
    }

    public function getRemarks(): string
    {
        return '';
    }
}
