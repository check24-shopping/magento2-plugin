<?php

namespace Check24\OrderImport\Model\Reader\OpenTrans\Xml;

use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataAddressInterface;
use DOMElement;

final class OpenTransAddressDocument implements OpenTransDataAddressInterface
{
    use DomHandlerTrait;

    public function __construct(DOMElement $domElement)
    {
        $this->domElement = $domElement;
    }

    public function getCompany(): string
    {
        return $this->getFirstTagValue("NAME");
    }

    public function getFirstname(): string
    {
        return $this->getFirstTagValue("NAME2");
    }

    public function getLastname(): string
    {
        $lastname = $this->getFirstTagValue("NAME3");

        $lastname = str_replace(' (nur Rechnungsadresse)', '', $lastname);

        return $lastname;
    }

    public function getStreet(): string
    {
        return $this->getFirstTagValue("STREET");
    }

    public function getRemarks(): string
    {
        return $this->getFirstTagValue("ADDRESS_REMARKS");
    }

    public function getZip(): string
    {
        return $this->getFirstTagValue("ZIP");
    }

    public function getCity(): string
    {
        return $this->getFirstTagValue("CITY");
    }

    public function getCountry(): string
    {
        return $this->getFirstTagValue("COUNTRY");
    }

    public function getCountryCode(): string
    {
        return $this->getFirstTagValue("COUNTRY_CODED");
    }

    public function getPhone(): string
    {
        return $this->getFirstTagValue("PHONE");
    }

    public function getEmail(): string
    {
        return $this->getFirstTagValue("EMAIL");
    }
}
