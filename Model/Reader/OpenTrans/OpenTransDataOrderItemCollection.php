<?php

namespace Check24\OrderImport\Model\Reader\OpenTrans;

use Countable;
use Iterator;

class OpenTransDataOrderItemCollection implements Countable, Iterator
{
    /** @var array|OpenTransDataOrderItemInterface[] */
    protected $collection = [];
    /** @var int */
    protected $position = 0;

    public function __construct(?OpenTransDataOrderItemInterface ...$orderItems)
    {
        if ($orderItems) {
            $this->collection = $orderItems;
        }
    }

    public function count(): int
    {
        return count($this->collection);
    }

    public function current(): ?OpenTransDataOrderItemInterface
    {
        return $this->collection[$this->position] ?? null;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->collection[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function isEmpty(): bool
    {
        return empty($this->collection);
    }

    public function add(OpenTransDataOrderItemInterface $orderItem): self
    {
        $this->collection[] = $orderItem;

        return $this;
    }
}
