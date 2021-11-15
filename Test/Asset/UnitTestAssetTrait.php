<?php

namespace Check24Shopping\OrderImport\Test\Asset;

trait UnitTestAssetTrait
{
    protected function getAsset(string $name): string
    {
        return file_get_contents(__DIR__ . '/' . $name);
    }
}
