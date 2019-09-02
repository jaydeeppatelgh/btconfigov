<?php
namespace Borntechies\LicensePlate\Api\Data;

/**
 * Interface ModelRepositoryInterface
 * @api
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
interface ModelInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const MODEL_ID                 = 'id';
    const HMDNR                    = 'hmdnr';
    const MAKE                     = 'make';
    const FUEL                     = 'fuel';
    const MOTOR_CODE               = 'motor_code';
    const GENERATION               = 'generation';
    const CONSTRUCTION_PERIOD      = 'construction_period';
    const MODEL                    = 'model';
    const INTRODUCTION_DATE       = 'introduction_date';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function  getId();

    /**
     * Get HMDNR number
     *
     * @return string
     */
    public function getHmdnr();

    /**
     * Get model
     *
     * @return string
     */
    public function getModel();


    /**
     * Get manufacturer
     *
     * @return string|null
     */
    public function getMake();

    /**
     * Get Fuel
     *
     * @return string|null
     */
    public function getFuel();

    /**
     * Get motor code
     *
     * @return string|null
     */
    public function getMotorCode();

    /**
     * Get generation
     *
     * @return string|null
     */
    public function getGeneration();

    /**
     * Get construction period
     *
     * @return string|null
     */
    public function getConstructionPeriod();

    /**
     * Set manufacturer
     *
     * @param string $make
     * @return ModelInterface
     */
    public function setMake($make);

    /**
     * Set model
     *
     * @param string $model
     *
     * @return ModelInterface
     */
    public function setModel($model);

    /**
     * Set fuel
     *
     * @param string $fuel
     *
     * @return ModelInterface
     */
    public function setFuel($fuel);

    /**
     * Set Motor Code
     *
     * @param string $motorCode
     *
     * @return ModelInterface
     */
    public function setMotorCode($motorCode);

    /**
     * Set Generation
     *
     * @param string $generation
     *
     * @return ModelInterface
     */
    public function setGeneration($generation);

    /**
     * Set Construction period
     *
     * @param string $constructionPeriod
     *
     * @return ModelInterface
     */
    public function setConstructionPeriod($constructionPeriod);

    /**
     * Introduction date
     *
     * @return string
     */
    public function getIntroductionDate();

    /**
     * Set Introduction date
     *
     * @param string $date
     *
     * @return ModelInterface
     */
    public function setIntroductionDate($date);

}