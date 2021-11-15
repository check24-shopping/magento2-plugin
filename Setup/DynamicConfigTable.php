<?php

namespace Check24Shopping\OrderImport\Setup;

use Check24Shopping\OrderImport\Api\Data\DynamicConfigInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class DynamicConfigTable implements TableInterface
{
    /** @var Table */
    private $table;

    public function __construct(SchemaSetupInterface $setup)
    {
        $this
            ->table = $setup
            ->getConnection()
            ->newTable($setup->getTable(DynamicConfigInterface::TABLE_NAME))
            ->addColumn(
                DynamicConfigInterface::FIELD_ID,
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'nullable' => false]
            )
            ->addColumn(
                DynamicConfigInterface::FIELD_PROCESS_CANCEL,
                Table::TYPE_BOOLEAN,
                null,
                [],
                'Process cancel documents from Check24'
            )
            ->addColumn(
                DynamicConfigInterface::FIELD_SEND_CANCEL,
                Table::TYPE_BOOLEAN,
                null,
                [],
                'Send canceled orders to Check24'
            )
            ->addColumn(
                DynamicConfigInterface::FIELD_SEND_DISPATCH,
                Table::TYPE_BOOLEAN,
                null,
                [],
                'Send dispatch notification to Check24'
            )
            ->addColumn(
                DynamicConfigInterface::FIELD_SEND_RETURN,
                Table::TYPE_BOOLEAN,
                null,
                [],
                'Send return documents to Check24'
            )
            ->addColumn(
                DynamicConfigInterface::FIELD_PROCESS_RETURN,
                Table::TYPE_BOOLEAN,
                null,
                [],
                'Process return documents from Check24'
            );
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getName(): string
    {
        return DynamicConfigInterface::TABLE_NAME;
    }
}
