<?php

namespace Amasty\SecondModule\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.4', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(InstallSchema::TABLE_NAME),
                'email',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Email'
                ]
            );
        }

        $setup->endSetup();
    }
}
