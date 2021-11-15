<?php

namespace Check24\OrderImport\Test\Unit;

use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataAddressInterface;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderItemCollection;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataPartyInterface;
use Check24\OrderImport\Model\Reader\OpenTrans\Xml\OpenTransOrderDocument;
use Check24\OrderImport\Test\Asset\UnitTestAssetTrait;
use PHPUnit\Framework\TestCase;

class OpenTransOrderDocumentTest extends TestCase
{
    use UnitTestAssetTrait;

    /** @var OpenTransOrderDocument */
    private $document;

    public function setUp(): void
    {
        $this->document = new OpenTransOrderDocument($this->getAsset('order.xml'));
    }

    public function testOrderDocument()
    {
        $this->assertEquals(33, $this->document->getDocumentId());
        $this->assertEquals('27586', $this->document->getPartnerId());
        $this->assertEquals('VC6HKS', $this->document->getOrderId());
        $this->assertEquals(0.00, $this->document->getShippingAmount());

        $this->assertTrue($this->document->getDeliveryParty() instanceof OpenTransDataPartyInterface);
        $this->assertTrue($this->document->getOrderItems() instanceof OpenTransDataOrderItemCollection);
        $this->assertIsArray($this->document->getParties());
    }

    public function testDeliveryParty()
    {
        $party = $this->document->getDeliveryParty();
        $this->assertTrue($party instanceof OpenTransDataPartyInterface);
        $this->assertEquals('check24', $party->getIdType());
        $this->assertEquals('delivery', $party->getRole());
        $this->invoiceAddressTest($party->getAddress());
    }

    private function invoiceAddressTest(OpenTransDataAddressInterface $address): void
    {
        $this->assertTrue($address instanceof OpenTransDataAddressInterface);
        $this->assertEquals('80636', $address->getZip());
        $this->assertEquals('Erika-Mann-Str. 66', $address->getStreet());
        $this->assertEquals('CHECK24', $address->getLastname());
        $this->assertEquals('Elektronik', $address->getFirstname());
        $this->assertEquals('CHECK24 Vergleichsportal Shopping GmbH', $address->getCompany());
        $this->assertEquals('DE', $address->getCountryCode());
        $this->assertEquals('Deutschland', $address->getCountry());
        $this->assertEquals('', $address->getRemarks());
        $this->assertEquals('c-egdgouhemdihuaakmefogd-adaefkkgke@ch24.de', $address->getEmail());
    }

    public function testSupplierParty()
    {
        $party = $this->document->getSupplierParty();
        $this->assertTrue($party instanceof OpenTransDataPartyInterface);
        $this->assertEquals('check24', $party->getIdType());
        $this->assertEquals('supplier', $party->getRole());
        $address = $party->getAddress();
        $this->assertTrue($address instanceof OpenTransDataAddressInterface);
        $this->assertEquals('5928 SK', $address->getZip());
        $this->assertEquals('Mary Kingsleystraat 1', $address->getStreet());
        $this->assertEquals('Venlo', $address->getCity());
        $this->assertEquals('', $address->getFirstname());
        $this->assertEquals('Vida XL Europe B.V.', $address->getCompany());
        $this->assertEquals('', $address->getCountryCode());
        $this->assertEquals('', $address->getCountry());
        $this->assertEquals('', $address->getRemarks());
        $this->assertEquals('', $address->getEmail());
    }
}
