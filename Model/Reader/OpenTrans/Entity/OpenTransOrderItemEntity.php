<?php

namespace Check24\OrderImport\Model\Reader\OpenTrans\Entity;

use Check24\OrderImport\Model\Reader\OpenTrans\OpenTransDataOrderItemInterface;

final class OpenTransOrderItemEntity implements OpenTransDataOrderItemInterface
{

    /** @var string */
    private $sku;
    /** @var int */
    private $quantity;
    /** @var float */
    private $price;
    /** @var string */
    private $descriptionShort;
    /** @var string */
    private $id;
    /** @var string */
    private $unit;

    public function __construct(
        string $sku,
        int    $quantity,
        float  $price,
        string $descriptionShort,
        string $id,
        string $unit
    )
    {
        $this->sku = $sku;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->descriptionShort = $descriptionShort;
        $this->id = $id;
        $this->unit = $unit;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDescriptionShort(): string
    {
        return $this->descriptionShort;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }
}
