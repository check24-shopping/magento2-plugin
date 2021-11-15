<?php


namespace Check24\OrderImport\Setup;


use Check24\OrderImport\Api\Data\OrderMappingInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class OrderMappingTable implements TableInterface
{
    /** @var Table */
    private $table;

    public function __construct(SchemaSetupInterface $setup)
    {
        $this
            ->table = $setup->getConnection()
            ->newTable($setup->getTable(OrderMappingInterface::TABLE_NAME))
            ->addColumn(
                OrderMappingInterface::FIELD_ID,
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'nullable' => false]
            )
            ->addColumn(
                OrderMappingInterface::FIELD_CHECK24_ORDER_ID,
                Table::TYPE_TEXT,
                25,
                [],
                'CHECK24 order ID'
            )
            ->addColumn(
                OrderMappingInterface::FIELD_PARTNER_ID,
                Table::TYPE_TEXT,
                25,
                [],
                'CHECK24 partner ID'
            )
            ->addColumn(
                OrderMappingInterface::FIELD_PARTY_SUPPLIER_ID,
                Table::TYPE_TEXT,
                25,
                [],
                'party supplier ID'
            )
            ->addColumn(
                OrderMappingInterface::FIELD_PARTY_DELIVERY_ID,
                Table::TYPE_TEXT,
                25,
                [],
                'party delivery ID'
            )
            ->addColumn(
                OrderMappingInterface::FIELD_PARTY_INVOICE_ISSUER_ID,
                Table::TYPE_TEXT,
                25,
                [],
                'party invoice issuer ID'
            )
            ->addColumn(
                OrderMappingInterface::FIELD_MAGENTO_ORDER_ID,
                Table::TYPE_INTEGER,
                null,
                [],
                'Magento order ID'
            )
            ->addColumn(
                OrderMappingInterface::FIELD_MAGENTO_ORDER_INCREMENT_ID,
                Table::TYPE_TEXT,
                32,
                [],
                'Magento order increment ID'
            )
            ->addColumn(
                OrderMappingInterface::FIELD_CREATED_AT,
                Table::TYPE_DATETIME,
                null,
                [],
                'Created at'
            )
            ->addIndex(
                'IDX_' . OrderMappingInterface::FIELD_MAGENTO_ORDER_ID,
                [OrderMappingInterface::FIELD_MAGENTO_ORDER_ID],
                ['type' => AdapterInterface::INDEX_TYPE_INDEX]
            );
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getName(): string
    {
        return OrderMappingInterface::TABLE_NAME;
    }
}
