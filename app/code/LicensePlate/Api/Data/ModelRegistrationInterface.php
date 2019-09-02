<?php
namespace Borntechies\LicensePlate\Api\Data;

/**
 * Interface ModelRegistrationInterface
 * @api
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
interface ModelRegistrationInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const MODEL_ID        = 'model_id';
    const REGISTRATION    = 'registration';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function  getId();

    /**
     * Get Model ID
     *
     * @return string|null
     */
    public function getModelId();

    /**
     * Get registration
     *
     * @return string|null
     */
    public function getRegistration();

    /**
     * Set registration
     *
     * @param string $registration
     *
     * @return ModelRegistrationInterface
     */
    public function setRegistration($registration);

    /**
     * Set model id
     *
     * @param int $id
     *
     * @return ModelRegistrationInterface
     */
    public function setModelId($id);
}