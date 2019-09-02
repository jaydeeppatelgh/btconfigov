<?php
namespace Borntechies\LicensePlate\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface ModelProductRepositoryInterface *
 * @api
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
interface ModelProductRepositoryInterface
{
    /**
     * Save model product link.
     *
     * @param  Data\ModelProductInterface $modelProduct
     *
     * @return Data\ModelProductInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function save(Data\ModelProductInterface $modelProduct);

    /**
     * Get model linked products list
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return Data\ModelProductSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete model product link.
     *
     * @param Data\ModelProductInterface $modelProduct
     *
     * @return bool true on success
     * @throws LocalizedException
     * @throws CouldNotDeleteException
     */
    public function delete(Data\ModelProductInterface $modelProduct);

    /**
     * Retrieve model product.
     *
     * @param int $modelId
     * @param int $productId
     *
     * @return Data\ModelProductInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function get($modelId, $productId);

    /**
     * Get all products for the model
     *
     * @param Data\ModelInterface $model
     *
     * @return Data\ModelProductInterface[]
     */
    public function getModelProducts(Data\ModelInterface $model);
}