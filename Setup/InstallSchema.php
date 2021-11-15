<?php

namespace Check24Shopping\OrderImport\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        foreach ($this->getTables($setup) as $table) {
            $setup->getConnection()->dropTable($table->getName());
            $setup->getConnection()->createTable($table->getTable());
        }
    }

    /** @return array|TableInterface[] */
    private function getTables(SchemaSetupInterface $setup): array
    {
        return [
            new ImportTable($setup),
            new ShipmentTable($setup),
            new OrderMappingTable($setup),
            new OrderPositionMappingTable($setup),
            new DocumentTrackingTable($setup),
            new CancelTable($setup),
            new ReturnTable($setup),
            new DynamicConfigTable($setup),
        ];
    }
}
