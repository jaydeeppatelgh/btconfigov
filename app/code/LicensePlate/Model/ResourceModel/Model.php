<?php
namespace Borntechies\LicensePlate\Model\ResourceModel;

use Borntechies\LicensePlate\Api\Data\ModelInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Model
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Model extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('license_plate_model', 'id');
    }

    /**
     * Get all existing models
     *
     * @return array
     */
    public function getManufacturers()
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), [ModelInterface::MAKE])
            ->order(ModelInterface::MAKE)
            ->distinct(true);
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Get all existing models by manufacturer
     *
     * @return array
     */
    public function getModels($manufacturer)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), [ModelInterface::MODEL])
            ->where("make = ?", $manufacturer)
            ->order(ModelInterface::MODEL)
            ->distinct(true);
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Get possible motors by model and manufacturer
     *
     * @param  string $manufacturer
     * @param string $model
     * @param string $generation
     * @param string $constructionPeriod
     *
     * @return array
     */
    public function getMotors($manufacturer, $model, $generation, $constructionPeriod)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), [ModelInterface::MODEL_ID, ModelInterface::FUEL, ModelInterface::MOTOR_CODE])
            ->where('model = ?', $model)
            ->where('make = ?', $manufacturer)
            ->where('introduction_date = ?', $constructionPeriod)
            ->where('generation = ?', $generation)
            ->order([ModelInterface::FUEL, ModelInterface::MOTOR_CODE]);
        return $this->getConnection()->fetchAll($select);
    }

    /**
     * Get generations by model, motor and fuel
     *
     * @param string $manufacturer
     * @param string $model
     *
     * @return array
     */
    public function getGenerations($manufacturer, $model)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), [ModelInterface::GENERATION])
            ->where('model = ?', $model)
            ->where('make = ?', $manufacturer)
            ->order(ModelInterface::GENERATION)
            ->distinct(true);
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Get construction period for selected parameters
     *
     * @param string $manufacturer
     * @param string $model
     * @param string $generation
     *
     * @return array
     */
    public function getConstructionPeriod($manufacturer, $model, $generation)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), [ModelInterface::INTRODUCTION_DATE])
            ->where('model = ?', $model)
            ->where('make = ?', $manufacturer)
            ->where('generation = ?', $generation)
            ->order(ModelInterface::INTRODUCTION_DATE)
            ->distinct(true);
        return $this->getConnection()->fetchCol($select);
    }
}