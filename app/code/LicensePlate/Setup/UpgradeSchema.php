<?php
/**
 *
 */
namespace Borntechies\LicensePlate\Setup;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the LicensePlate module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            /**
             * Add index 'registration'
             */
            $setup->getConnection()->addIndex(
                $setup->getTable('license_plate_registration'),
                $setup->getConnection()->getIndexName(
                    $setup->getTable('license_plate_registration'),
                    'registration',
                    'index'
                ),
                'registration'
            );
        }

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('license_plate_model'),
                'introduction_date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 10,
                    'nullable' => true,
                    'comment' => 'Introduction Date'
                ]
            );
        }
        $setup->endSetup();
    }
}