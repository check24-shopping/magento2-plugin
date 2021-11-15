<?php

namespace Check24\OrderImport\Setup;

use Check24\OrderImport\Api\Data\Check24ShipmentInterface;
use Check24\OrderImport\Api\Data\OrderImportInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class ShipmentTable implements TableInterface
{
    /** @var Table */
    private $table;

    public function __construct(SchemaSetupInterface $setup)
    {
        $this
            ->table = $setup
            ->getConnection()
            ->newTable($setup->getTable(Check24ShipmentInterface::TABLE_NAME))
            ->addColumn(
                Check24ShipmentInterface::FIELD_ID,
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'nullable' => false]
            )
            ->addColumn(
                Check24ShipmentInterface::FIELD_SHIPMENT_ID,
                Table::TYPE_INTEGER,
                null,
                [],
                'Magento Order ID'
            )
            ->addColumn(
                Check24ShipmentInterface::FIELD_STATUS,
                Table::TYPE_INTEGER,
                1,
                ['default' => 0],
                'Status'
            )
            ->addColumn(
                OrderImportInterface::FIELD_ERROR_MESSAGE,
                Table::TYPE_TEXT,
                null,
                [],
                'Error Message'
            )
            ->addColumn(
                Check24ShipmentInterface::FIELD_CREATED_AT,
                Table::TYPE_DATETIME,
                null,
                [],
                'Created At'
            )
            ->addIndex(
                'IDX_' . Check24ShipmentInterface::FIELD_STATUS,
                [Check24ShipmentInterface::FIELD_STATUS],
                ['type' => AdapterInterface::INDEX_TYPE_INDEX]
            );
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getName(): string
    {
        return Check24ShipmentInterface::TABLE_NAME;
    }
}
