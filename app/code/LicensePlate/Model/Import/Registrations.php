<?php
namespace Borntechies\LicensePlate\Model\Import;

use Borntechies\LicensePlate\Api\Data\ModelRegistrationInterface;
use Borntechies\LicensePlate\Api\Data\ModelInterface;
use Borntechies\LicensePlate\Helper\Validation;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Registrations
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Registrations extends AbstractImport
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
            ModelRegistrationInterface::MODEL_ID => [
                'type' => [
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null
                ],
                'nullable' => true,
                'unsigned' => false,
                'comment' => ''
            ],
            ModelRegistrationInterface::REGISTRATION  => [
                'type' => [
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255
                ],
                'nullable' => false,
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validateCsvRow($row)
    {
        $validationResult = Validation::validateRegistrations($row);
        if ($validationResult !== true) {
            $this->errors = array_merge($this->errors, $validationResult);
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function updateSource()
    {
        $this->updateTmpModelField();
        if (!$this->updateMainTableFromTmp()) {
            throw new LocalizedException(__('Cannot update registration info'));
        };
        $this->checkForMissedModels();
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
                ->joinLeft(['tmp' =>  $this->addTemporaryTableSuffix()],
                    'main_table.hmdnr = tmp.hmdnr',
                    ['id', ModelRegistrationInterface::MODEL_ID, ModelRegistrationInterface::REGISTRATION]
                )
                ->where('tmp.model_id is not null');

            $query = $this->connection->insertFromSelect(
                $select,
                ['main_table' => $this->getTableName()], ['id', ModelRegistrationInterface::MODEL_ID, ModelRegistrationInterface::REGISTRATION]
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
     * Check if there are any rows that can't be imported from the tmp table
     *
     * @return bool
     */
    private function checkForMissedModels()
    {
        $select = $this->connection->select()
            ->from($this->addTemporaryTableSuffix(), [ModelInterface::HMDNR])
            ->distinct(true)
            ->where('model_id is null');
        $modelNotFound = $this->connection->fetchCol($select);
        if ($modelNotFound) {
            $this->errors[] = __('Next model HMDNR are not found during registrations import: %1', implode(', ', $modelNotFound));
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function checkCsvFileHeaders($headers)
    {
        $errors = [];
        foreach (Validation::$requiredRegistrationFields as $field) {
            if (!in_array($field, $headers)) {
                $errors[] = __('Required field %1 is missed in registration import', $field);
            }
        }

        if (!$errors) {
            return true;
        }

        return $errors;
    }
}