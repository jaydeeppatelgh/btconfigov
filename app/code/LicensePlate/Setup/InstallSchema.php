<?php
namespace Borntechies\LicensePlate\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @codeCoverageIgnore
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'license_plate_model'
         */
        $modelTable = $installer->getConnection()->newTable(
                $installer->getTable('license_plate_model')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Model Id'
            )->addColumn(
                'hmdnr',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'HMDNR'
            )->addColumn(
                'make',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                ['nullable' => false],
                'Make'
            )->addColumn(
                'fuel',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                1,
                ['nullable' => false],
                'Fuel'
            )->addColumn(
                'model',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                ['nullable' => false],
                'Model'
            )->addColumn(
                'motor_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Motor Code'
            )->addColumn(
                'generation',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Generation'
            )->addColumn(
                'construction_period',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Construction Period'
            )->addIndex(
                $installer->getIdxName('license_plate_model', ['hmdnr']),
                ['hmdnr']
            )->setComment(
                'License Plate Model'
            );
        $installer->getConnection()->createTable($modelTable);

        /**
         * Create table 'license_plate_registration'
         */
        $registrationTable = $installer->getConnection()->newTable(
                $installer->getTable('license_plate_registration')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Generation Id'
            )->addColumn(
                'model_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Model Id'
            )->addColumn(
                'registration',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Registration'
            )->addForeignKey(
                $installer->getFkName('license_plate_registration', 'model_id', 'license_plate_model', 'id'),
                'model_id',
                $installer->getTable('license_plate_model'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'License Plate Registration'
            );
        $installer->getConnection()->createTable($registrationTable);

        /**
         * Create table 'license_plate_product'
         */
        $productTable = $installer->getConnection()->newTable(
            $installer->getTable('license_plate_product')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Generation Id'
        )->addColumn(
            'model_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Model Id'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Product ID'
        )->addForeignKey(
            $installer->getFkName('license_plate_product', 'model_id', 'license_plate_model', 'id'),
            'model_id',
            $installer->getTable('license_plate_model'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('license_plate_product', 'product_id', 'catalog_product_entity', 'entity_id'),
            'product_id',
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'License Plate Product'
        );
        $installer->getConnection()->createTable($productTable);
    }
}