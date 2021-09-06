<?php

namespace Amasty\SecondModule\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
        const TABLE_NAME = 'Amasty_SecondModule_blacklist';

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $table = $setup->getConnection()
            ->newTable($setup->getTable(self::TABLE_NAME))
            -> addColumn(
                'blacklist_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Blaclist Id'
            )
            ->addColumn(
                'product_sku',
                Table::TYPE_TEXT,
                '255',
                [
                    'nullable' => false,
                    'default' => ''
                ],
                'Sku for the product'
            )
            ->addColumn(
                'product_qty',
                Table::TYPE_INTEGER,
                15,
                [
                    'nullable' => true,
                    'default' => null
                ],
                'Qty for the product'
            )
            ->setComment('Blacklist Table');
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
