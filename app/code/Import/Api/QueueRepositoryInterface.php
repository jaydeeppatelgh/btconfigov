<?php
namespace Borntechies\Import\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Borntechies\Import\Api\Data\QueueInterface;
use Borntechies\Import\Api\Data\QueueCollectionInterface;

/**
 * Interface QueueRepositoryInterface
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
interface QueueRepositoryInterface
{
    /**
     * Save queue item.
     *
     * @param QueueInterface $queue
     *
     * @return QueueInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function save(QueueInterface $queue);

    /**
     * Retrieve queue by id.
     *
     * @param int $id
     *
     * @return QueueInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function get($id);

    /**
     * Retrieve queues matching the specified criteria.
     *
     * @param QueueCriteriaInterface $searchCriteria
     *
     * @return QueueCollectionInterface
     * @throws LocalizedException
     */
    public function getList(QueueCriteriaInterface $searchCriteria);

    /**
     * Delete queue item.
     *
     * @param QueueInterface $queue
     *
     * @return bool true on success
     * @throws CouldNotDeleteException
     * @throws LocalizedException
     */
    public function delete(QueueInterface $queue);

    /**
     * Delete queue by ID.
     *
     * @param int $queueId
     *
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($queueId);
}