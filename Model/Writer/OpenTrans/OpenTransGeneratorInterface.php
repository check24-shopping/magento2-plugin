<?php


namespace Check24Shopping\OrderImport\Model\Writer\OpenTrans;


interface OpenTransGeneratorInterface
{
    public function getXmlString(): string;
}
