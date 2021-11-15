<?php


namespace Check24Shopping\OrderImport\Setup;


use Check24Shopping\OrderImport\Api\Data\OrderPositionMappingInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class OrderPositionMappingTable implements TableInterface
{
    /** @var Table */
    private $table;

    public function __construct(SchemaSetupInterface $setup)
    {
        $this
            ->table = $setup->getConnection()
            ->newTable($setup->getTable(OrderPositionMappingInterface::TABLE_NAME))
            ->addColumn(
                OrderPositionMappingInterface::FIELD_ID,
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'nullable' => false]
            )
            ->addColumn(
                OrderPositionMappingInterface::FIELD_CHECK24_ORDER_ID,
                Table::TYPE_TEXT,
                25,
                [],
                'CHECK24 Order ID'
            )
            ->addColumn(
                OrderPositionMappingInterface::FIELD_CHECK24_POSITION_ID,
                Table::TYPE_TEXT,
                40,
                [],
                'CHECK24 Position ID'
            )
            ->addColumn(
                OrderPositionMappingInterface::FIELD_MAGENTO_POSITION_ID,
                Table::TYPE_INTEGER,
                null,
                [],
                'Magento Position ID'
            )
            ->addColumn(
                OrderPositionMappingInterface::FIELD_ORDER_UNIT,
                Table::TYPE_TEXT,
                25,
                [],
                'CHECK24 order unit'
            );
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getName(): string
    {
        return OrderPositionMappingInterface::TABLE_NAME;
    }
}
