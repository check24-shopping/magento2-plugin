<?php


namespace Check24\OrderImport\Model\Writer\OpenTrans;


interface OpenTransGeneratorInterface
{
    public function getXmlString(): string;
}
