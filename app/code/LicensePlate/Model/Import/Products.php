<?php
namespace Borntechies\LicensePlate\Model\Import;

use Borntechies\LicensePlate\Api\Data\ModelProductInterface;
use Borntechies\LicensePlate\Api\Data\ModelInterface;
use Borntechies\LicensePlate\Helper\Validation;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Products
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Products extends AbstractImport
{
    /**
     * {@inheritdoc}
     */
    protected function getTmpTableColumns()
    {
        return [
            'id' => [
                'type' => [
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null
                ],
                'nullable' => false,
                'unsigned' => true,
                'primary' => true,
                'comment' => ''
            ],
            ModelProductInterface::MODEL_ID => [
                'type' => [
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null
                ],
                'nullable' => true,
                'unsigned' => false,
                'comment' => ''
            ],
            ModelProductInterface::PRODUCT_ID => [
                'type' => [
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100
                ],
                'nullable' => true,
                'unsigned' => false,
                'comment' => ''
            ],
            ModelInterface::HMDNR => [
                'type' => [
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null
                ],
                'nullable' => false,
                'unsigned' => false,
                'comment' => ''
            ],
            ProductInterface::SKU => [
                'type' => [
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    64
                ],
                'nullable' => false,
                'unsigned' => false,
                'comment' => ''
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateSource()
    {
        $this->updateTmpModelField();
        $this->updateProductField();
        if (!$this->updateMainTableFromTmp()) {
            throw new LocalizedException(__('Cannot update products info'));
        };
        $this->checkEmptyTmpRows();
    }

    /**
     * Check if there are any rows that can't be imported from the tmp table
     *
     * @return bool
     */
    private function checkEmptyTmpRows()
    {
        $tmpTable = $this->addTemporaryTableSuffix();
        //add missed product to log
        $select = $this->connection->select()
            ->from($tmpTable, ['sku'])
            ->distinct(true)
            ->where('product_id is null');
        $skuNotFound = $this->connection->fetchCol($select);
        if ($skuNotFound) {
            $this->errors[] = __('Next product skus are not found during licensepalet import: %1', implode(', ', $skuNotFound));
        }

        $select = $this->connection->select()
            ->from($tmpTable, [ModelInterface::HMDNR])
            ->distinct(true)
            ->where('model_id is null');
        $modelNotFound = $this->connection->fetchCol($select);
        if ($modelNotFound) {
            $this->errors[] = __('Next model HMDNR are not found during licensepalet import: %1', implode(', ', $modelNotFound));
        }

        if ($skuNotFound || $modelNotFound) {
            return true;
        }

        return false;
    }

    /**
     * Add product_id to tmp table
     *
     * @return void
     */
    private function updateProductField()
    {
        $productTable = 'catalog_product_entity';
        $subSelect = $this->connection->select()->from(
            $this->getTableName($productTable),
            ['entity_id', 'sku']
        );
        $select = $this->connection->select()->join(
            ['product_entity' => $subSelect],
            'tmp_table.sku = product_entity.sku',
            ['product_id' => 'product_entity.entity_id']
        );
        $updateQuery = $select->crossUpdateFromSelect(['tmp_table' => $this->addTemporaryTableSuffix()]);
        $this->connection->query($updateQuery);
    }

    /**
     * Update main table from tmp
     *
     * @return bool
     */
    private function updateMainTableFromTmp()
    {
        try {
            $this->connection->beginTransaction();
            $this->connection->delete($this->getTableName());
            $select = $this->connection->select()
                ->joinLeft(['tmp' => $this->addTemporaryTableSuffix()],
                    'main_table.hmdnr = tmp.hmdnr',
                    ['id', ModelProductInterface::MODEL_ID, ModelProductInterface::PRODUCT_ID]
                )
                ->where('tmp.product_id is not null')
                ->where('tmp.model_id is not null');

            $query = $this->connection->insertFromSelect(
                $select,
                ['main_table' => $this->getTableName()], ['id', ModelProductInterface::MODEL_ID, ModelProductInterface::PRODUCT_ID]
            );
            $this->connection->query($query);
            $this->connection->commit();

        } catch (\Exception $e) {
            $this->connection->rollBack();
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function validateCsvRow($row)
    {
        $validationResult = Validation::validateProducts($row);
        if ($validationResult !== true) {
            $this->errors = array_merge($this->errors, $validationResult);
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function checkCsvFileHeaders($headers)
    {
        $errors = [];
        foreach (Validation::$requiredProductFields as $field) {
            if (!in_array($field, $headers)) {
                $errors[] = __('Required field %1 is missed in product import', $field);
            }
        }

        if (!$errors) {
            return true;
        }

        return $errors;
    }
}