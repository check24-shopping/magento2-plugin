<?php

namespace Check24Shopping\OrderImport\Model\Reader\OpenTrans\Xml;

use DOMElement;

trait DomHandlerTrait
{
    /** @var DOMElement */
    protected $domElement;

    private function getFirstTagValue(string $tagName, DOMElement $domElement = null): string
    {
        if ($domElement === null) {
            $domElement = $this->domElement;
        }
        $elements = $domElement->getElementsByTagName($tagName);

        return $elements->length === 0 ? '' : (string)$elements->item(0)->nodeValue;
    }
}
