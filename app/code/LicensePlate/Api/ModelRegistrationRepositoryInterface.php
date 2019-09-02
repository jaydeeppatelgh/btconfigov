<?php
namespace Borntechies\LicensePlate\Api;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface ModelRegistrationRepositoryInterface
 * @api
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
interface ModelRegistrationRepositoryInterface
{
    /**
     * Get model registration list
     *
     * @param Data\ModelInterface $model
     *
     * @return Data\ModelRegistrationInterface[]
     * @throws LocalizedException
     */
    public function getList(Data\ModelInterface $model);

    /**
     * Retrieve registration by id.
     *
     * @param int $id
     *
     * @return Data\ModelRegistrationInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function get($id);

    /**
     * Delete registration.
     *
     * @param Data\ModelRegistrationInterface $registration
     *
     * @return bool true on success
     * @throws CouldNotDeleteException
     * @throws LocalizedException
     */
    public function delete(Data\ModelRegistrationInterface $registration);

    /**
     * Save registration.
     *
     * @param  Data\ModelRegistrationInterface $registration
     *
     * @return Data\ModelRegistrationInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function save(Data\ModelRegistrationInterface $registration);

    /**
     * Retrieve model registration by number.
     *
     * @param string $number
     *
     * @return Data\ModelRegistrationInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getByRegistration($number);
}