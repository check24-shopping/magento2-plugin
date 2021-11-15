<?php

namespace Check24Shopping\OrderImport\Test\Unit;

use Check24Shopping\OrderImport\Model\Reader\OpenTrans\Xml\OpenTransDocument;
use Check24Shopping\OrderImport\Test\Asset\UnitTestAssetTrait;
use PHPUnit\Framework\TestCase;

class OpenTransDocumentTest extends TestCase
{
    use UnitTestAssetTrait;

    public function testOrderDocument()
    {
        $document = new OpenTransDocument($this->getAsset('order.xml'));

        $this->assertEquals(33, $document->getDocumentId());
        $this->assertEquals('order', $document->getType());
        $this->assertEquals('VC6HKS', $document->getOrderId());
    }

    public function testCancelDocument()
    {
        $document = new OpenTransDocument($this->getAsset('cancel.xml'));

        $this->assertEquals(34, $document->getDocumentId());
        $this->assertEquals('orderchange', $document->getType());
        $this->assertEquals('VC6HKS', $document->getOrderId());
    }
}
