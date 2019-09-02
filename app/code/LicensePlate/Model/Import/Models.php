<?php
namespace Borntechies\LicensePlate\Model\Import;

use Borntechies\LicensePlate\Api\Data\ModelInterface;
use Borntechies\LicensePlate\Helper\Validation;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Models
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Models extends AbstractImport
{
    /**
     * {@inheritdoc}
     */
    protected function getTmpTableColumns()
    {
        return [
            ModelInterface::HMDNR => [
                'type' => [
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null
                ],
                'nullable' => false,
                'unsigned' => false,
                'comment' => ''
            ],
            ModelInterface::MAKE => [
                'type' => [
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100
                ],
                'nullable' => false,
                'unsigned' => false,
                'comment' => ''
            ],
            ModelInterface::MODEL => [
                'type' => [
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    150
                ],
                'nullable' => false,
                'unsigned' => false,
                'comment' => ''
            ],
            ModelInterface::FUEL => [
                'type' => [
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    1
                ],
                'nullable' => false,
                'unsigned' => false,
                'comment' => ''
            ],
            ModelInterface::MOTOR_CODE => [
                'type' => [
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255
                ],
                'nullable' => true,
                'unsigned' => false,
                'comment' => ''
            ],
            ModelInterface::GENERATION => [
                'type' => [
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255
                ],
                'nullable' => true,
                'unsigned' => false,
                'comment' => ''
            ],
            ModelInterface::CONSTRUCTION_PERIOD => [
                'type' => [
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255
                ],
                'nullable' => true,
                'unsigned' => false,
                'comment' => ''
            ],
            ModelInterface::INTRODUCTION_DATE => [
                'type' => [
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    10
                ],
                'nullable' => true,
                'unsigned' => false,
                'comment' => ''
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateSource()
    {
        try {
            $this->connection->beginTransaction();
            //remove models that are not in the import csv file
            $select = $this->connection->select()
                ->from(['main_table' => $this->getTableName()], [ModelInterface::MODEL_ID, ModelInterface::HMDNR])
                ->joinLeft(['tmp' =>  $this->addTemporaryTableSuffix($this->getTableName())],
                    'main_table.hmdnr = tmp.hmdnr', ['hmdnr_tmp' => 'hmdnr'])
                ->where('tmp.hmdnr is null');
            $rowsToDelete = $this->connection->fetchCol($select);

            if ($rowsToDelete) {
                $this->connection->delete($this->getTableName(), ['id IN (?)' => $rowsToDelete]);
            }

            //update main table from tmp
            $columns = array_keys($this->connection->describeTable($this->addTemporaryTableSuffix()));
            $subSelect = $this->connection->select()->from(
                $this->addTemporaryTableSuffix(),
                $columns
            );
            $select = $this->connection->select()->join(
                ['tmp' => $subSelect],
                'main_table.hmdnr = tmp.hmdnr',
                $columns
            );
            $updateQuery = $select->crossUpdateFromSelect(['main_table' => $this->getTableName()]);
            $this->connection->query($updateQuery);

            //insert new models from tmp table
            $select = $this->connection->select()
                ->from(['tmp' =>$this->addTemporaryTableSuffix()], $columns)
                ->joinLeft(['main_table' =>  $this->getTableName()],
                    'main_table.hmdnr = tmp.hmdnr',
                    []
                )
                ->where('main_table.hmdnr is null');
            $query = $this->connection->insertFromSelect($select, ['main_table' => $this->getTableName()], $columns);
            $this->connection->query($query);
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw new LocalizedException(__('Cannot update models'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validateCsvRow($row)
    {
        $validationResult = Validation::validateModel($row);
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
        foreach (Validation::$requiredModelFields as $field) {
            if (!in_array($field, $headers)) {
                $errors[] = __('Required field %1 is missed in model import', $field);
            }
        }

        if (!$errors) {
            return true;
        }

        return $errors;
    }
}