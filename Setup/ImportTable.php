<?php


namespace Check24Shopping\OrderImport\Setup;


use Check24Shopping\OrderImport\Api\Data\OrderImportInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class ImportTable implements TableInterface
{
    /** @var Table */
    private $table;

    public function __construct(SchemaSetupInterface $setup)
    {
        $this
            ->table = $setup->getConnection()
            ->newTable($setup->getTable(OrderImportInterface::TABLE_NAME))
            ->addColumn(
                OrderImportInterface::FIELD_ID,
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'nullable' => false]
            )
            ->addColumn(
                OrderImportInterface::FIELD_CHECK24_ORDER_ID,
                Table::TYPE_TEXT,
                25,
                [],
                'CHECK24 Order ID'
            )
            ->addColumn(
                OrderImportInterface::FIELD_TYPE,
                Table::TYPE_TEXT,
                25,
                [],
                'CHECK24 Order ID'
            )
            ->addColumn(
                OrderImportInterface::FIELD_ACTION,
                Table::TYPE_TEXT,
                35,
                [],
                'CHECK24 Order ID'
            )
            ->addColumn(
                OrderImportInterface::FIELD_CONTENT,
                Table::TYPE_TEXT,
                null,
                [],
                'Content'
            )
            ->addColumn(
                OrderImportInterface::FIELD_STATUS,
                Table::TYPE_INTEGER,
                1,
                ['default' => 0],
                'Status'
            )
            ->addColumn(
                OrderImportInterface::FIELD_MAGENTO_ORDER_ID,
                Table::TYPE_INTEGER,
                null,
                [],
                'Magento Order ID'
            )
            ->addColumn(
                OrderImportInterface::FIELD_MAGENTO_ORDER_INCREMENT_ID,
                Table::TYPE_TEXT,
                32,
                [],
                'Magento Order Increment ID'
            )
            ->addColumn(
                OrderImportInterface::FIELD_ERROR_MESSAGE,
                Table::TYPE_TEXT,
                null,
                [],
                'Error Message'
            )
            ->addColumn(
                OrderImportInterface::FIELD_CREATED_AT,
                Table::TYPE_DATETIME,
                null,
                [],
                'Created At'
            )
            ->addIndex(
                'IDX_' . OrderImportInterface::FIELD_STATUS,
                [OrderImportInterface::FIELD_STATUS],
                ['type' => AdapterInterface::INDEX_TYPE_INDEX]
            )
            ->addIndex(
                'IDX_' . OrderImportInterface::FIELD_MAGENTO_ORDER_ID,
                [OrderImportInterface::FIELD_MAGENTO_ORDER_ID],
                ['type' => AdapterInterface::INDEX_TYPE_INDEX]
            );
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getName(): string
    {
        return OrderImportInterface::TABLE_NAME;
    }
}
