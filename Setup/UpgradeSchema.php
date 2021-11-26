<?php

namespace Check24Shopping\OrderImport\Setup;

use Check24Shopping\OrderImport\Api\Data\OrderImportInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $installer
                ->getConnection()
                ->addColumn(
                    OrderImportInterface::TABLE_NAME,
                    OrderImportInterface::FIELD_ERROR_DETAILS,
                    Table::TYPE_TEXT
                );
        }


        $installer->endSetup();
    }
}
