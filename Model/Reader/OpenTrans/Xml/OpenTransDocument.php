<?php

namespace Check24\OrderImport\Model\Reader\OpenTrans\Xml;

use Check24\OrderImport\Model\Exception\DocumentIdMissing;
use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDocumentInterface;
use DOMDocument;
use DOMElement;

final class OpenTransDocument implements OpenTransDocumentInterface
{
    use DomHandlerTrait;

    /** @var string */
    private $content;

    public function __construct(string $content)
    {
        $this->domElement = new DOMDocument();
        $this->domElement->loadXML($content);
        $this->content = $content;
    }

    public function getOrderId(): string
    {
        return (string)$this->getFirstTagValue('ORDER_ID');
    }

    public function getDocumentId(): string
    {
        /** @var DOMElement $element */
        foreach ($this->domElement->getElementsByTagName('REMARKS') as $element) {
            if ($element->getAttribute('type') === 'documentnumber') {
                return $element->nodeValue;
            }
        }

        throw new DocumentIdMissing('documentnumber is missing');
    }

    public function getType(): string
    {
        return strtolower($this->domElement->firstChild->nodeName);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAction(): string
    {
        /** @var DOMElement $element */
        foreach ($this->domElement->getElementsByTagName('REMARKS') as $element) {
            if ($element->getAttribute('type') === 'action') {
                return strtolower($element->nodeValue);
            }
        }

        return '';
    }
}
