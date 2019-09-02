<?php
namespace Borntechies\LicensePlate\Model;

use Magento\Framework\Model\AbstractModel;
use Borntechies\LicensePlate\Api\Data\ModelInterface;

/**
 * Class Model
 * @method ResourceModel\Model _getResource()
 * @method ResourceModel\Model getResource()
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Model extends AbstractModel implements ModelInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Borntechies\LicensePlate\Model\ResourceModel\Model::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getHmdnr()
    {
        return $this->getData(self::HMDNR);
    }

    /**
     * {@inheritdoc}
     */
    public function getMake()
    {
        return $this->getData(self::MAKE);
    }

    /**
     * {@inheritdoc}
     */
    public function getFuel()
    {
        return $this->getData(self::FUEL);
    }

    /**
     * {@inheritdoc}
     */
    public function getModel()
    {
        return $this->getData(self::MODEL);
    }

    /**
     * {@inheritdoc}
     */
    public function getMotorCode()
    {
        return $this->getData(self::MOTOR_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function getGeneration()
    {
        return $this->getData(self::GENERATION);
    }

    /**
     * {@inheritdoc}
     */
    public function getConstructionPeriod()
    {
        return $this->getData(self::CONSTRUCTION_PERIOD);
    }

    /**
     * {@inheritdoc}
     */
    public function setMake($make)
    {
        return $this->setData(self::MAKE, $make);
    }

    /**
     * {@inheritdoc}
     */
    public function setFuel($fuel)
    {
        return $this->setData(self::FUEL, $fuel);
    }

    /**
     * {@inheritdoc}
     */
    public function setModel($model)
    {
        return $this->setData(self::MODEL, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function setMotorCode($motorCode)
    {
        return $this->setData(self::MOTOR_CODE, $motorCode);
    }

    /**
     * {@inheritdoc}
     */
    public function setGeneration($generation)
    {
        return $this->setData(self::GENERATION, $generation);
    }

    /**
     * {@inheritdoc}
     */
    public function setConstructionPeriod($constructionPeriod)
    {
        return $this->setData(self::CONSTRUCTION_PERIOD, $constructionPeriod);
    }

    /**
     * {@inheritdoc}
     */
    public function getIntroductionDate()
    {
       return $this->getData(self::INTRODUCTION_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIntroductionDate($date)
    {
        return $this->setData(self::INTRODUCTION_DATE, $date);
    }
}