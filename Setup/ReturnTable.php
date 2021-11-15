<?php

namespace Check24Shopping\OrderImport\Setup;

use Check24Shopping\OrderImport\Api\Data\Check24ReturnInterface;
use Check24Shopping\OrderImport\Api\Data\OrderImportInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class ReturnTable implements TableInterface
{
    /** @var Table */
    private $table;

    public function __construct(SchemaSetupInterface $setup)
    {
        $this
            ->table = $setup
            ->getConnection()
            ->newTable($setup->getTable(Check24ReturnInterface::TABLE_NAME))
            ->addColumn(
                Check24ReturnInterface::FIELD_ID,
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'nullable' => false]
            )
            ->addColumn(
                Check24ReturnInterface::FIELD_ORDER_ID,
                Table::TYPE_INTEGER,
                null,
                [],
                'Magento Order ID'
            )
            ->addColumn(
                Check24ReturnInterface::FIELD_STATUS,
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
                Check24ReturnInterface::FIELD_CREATED_AT,
                Table::TYPE_DATETIME,
                null,
                [],
                'Created At'
            )
            ->addIndex(
                'IDX_' . Check24ReturnInterface::FIELD_STATUS,
                [Check24ReturnInterface::FIELD_STATUS],
                ['type' => AdapterInterface::INDEX_TYPE_INDEX]
            );
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getName(): string
    {
        return Check24ReturnInterface::TABLE_NAME;
    }
}
