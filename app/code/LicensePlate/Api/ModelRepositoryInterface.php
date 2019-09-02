<?php
namespace Borntechies\LicensePlate\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface BlockRepositoryInterface *
 * @api
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
interface ModelRepositoryInterface
{
    /**
     * Save model.
     *
     * @param  Data\ModelInterface $model
     *
     * @return Data\ModelInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function save(Data\ModelInterface $model);

    /**
     * Retrieve model by id.
     *
     * @param int $modelId
     *
     * @return Data\ModelInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function get($modelId);

    /**
     * Retrieve models matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return Data\ModelSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete model.
     *
     * @param Data\ModelInterface $model
     *
     * @return bool true on success
     * @throws CouldNotDeleteException
     * @throws LocalizedException
     */
    public function delete(Data\ModelInterface $model);

    /**
     * Delete model by ID.
     *
     * @param int $modelId
     *
     * @return bool true on success
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($modelId);
}