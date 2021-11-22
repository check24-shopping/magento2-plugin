<?php


namespace Check24Shopping\OrderImport\Setup;

use Check24Shopping\OrderImport\Api\Data\ReturnRequestInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class ReturnRequestTable implements TableInterface
{
    /** @var Table */
    private $table;

    public function __construct(SchemaSetupInterface $setup)
    {
        $this
            ->table = $setup->getConnection()
            ->newTable($setup->getTable(ReturnRequestInterface::TABLE_NAME))
            ->addColumn(
                ReturnRequestInterface::FIELD_ID,
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'nullable' => false]
            )
            ->addColumn(
                ReturnRequestInterface::FIELD_CHECK24_ORDER_ID,
                Table::TYPE_TEXT,
                25,
                [],
                'CHECK24 order ID'
            )
            ->addColumn(
                ReturnRequestInterface::FIELD_MAGENTO_ORDER_ID,
                Table::TYPE_INTEGER,
                null,
                [],
                'Magento order ID'
            )
            ->addColumn(
                ReturnRequestInterface::FIELD_MAGENTO_ORDER_INCREMENT_ID,
                Table::TYPE_TEXT,
                32,
                [],
                'Magento order increment ID'
            )
            ->addColumn(
                ReturnRequestInterface::FIELD_CREATED_AT,
                Table::TYPE_DATETIME,
                null,
                [],
                'Created at'
            )
            ->addColumn(
                ReturnRequestInterface::FIELD_ORDER_CREATED_AT,
                Table::TYPE_DATETIME,
                null,
                [],
                'Created at'
            )
            ->addIndex(
                'IDX_' . ReturnRequestInterface::FIELD_MAGENTO_ORDER_ID,
                [ReturnRequestInterface::FIELD_MAGENTO_ORDER_ID],
                ['type' => AdapterInterface::INDEX_TYPE_INDEX]
            );
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getName(): string
    {
        return ReturnRequestInterface::TABLE_NAME;
    }
}
