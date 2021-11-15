<?php

namespace Check24Shopping\OrderImport\Model\Reader\OpenTrans;

interface OpenTransDataAddressInterface
{
    public function getCompany(): string;

    public function getFirstname(): string;

    public function getLastname(): string;

    public function getStreet(): string;

    public function getZip(): string;

    public function getCity(): string;

    public function getCountry(): string;

    public function getCountryCode(): string;

    public function getPhone(): string;

    public function getEmail(): string;

    public function getRemarks(): string;
}
