<?php

namespace Check24\OrderImport\Setup;

use Check24\OrderImport\Api\Data\DocumentTrackingInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class DocumentTrackingTable implements TableInterface
{
    /** @var Table */
    private $table;

    public function __construct(SchemaSetupInterface $setup)
    {
        $this
            ->table = $setup
            ->getConnection()
            ->newTable($setup->getTable(DocumentTrackingInterface::TABLE_NAME))
            ->addColumn(
                DocumentTrackingInterface::FIELD_ID,
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'nullable' => false]
            )
            ->addColumn(
                DocumentTrackingInterface::FIELD_DOCUMENT_ID,
                Table::TYPE_INTEGER,
                null,
                [],
                'Check24 Document ID'
            )
            ->addIndex(
                'IDX_' . DocumentTrackingInterface::FIELD_DOCUMENT_ID,
                [DocumentTrackingInterface::FIELD_DOCUMENT_ID],
                ['type' => AdapterInterface::INDEX_TYPE_INDEX]
            );
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function getName(): string
    {
        return DocumentTrackingInterface::TABLE_NAME;
    }
}
