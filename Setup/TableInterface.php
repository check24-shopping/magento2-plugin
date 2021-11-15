<?php

namespace Check24\OrderImport\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

interface TableInterface
{
    public function __construct(SchemaSetupInterface $setup);

    public function getTable(): Table;

    public function getName(): string;
}
