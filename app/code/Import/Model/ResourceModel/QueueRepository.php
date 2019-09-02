<?php

namespace Borntechies\Import\Model\ResourceModel;

use Borntechies\Import\Api\Data\QueueCollectionInterfaceFactory;
use Borntechies\Import\Api\Data\QueueInterface;
use Borntechies\Import\Api\QueueCriteriaInterface;
use Borntechies\Import\Api\QueueRepositoryInterface;
use Borntechies\Import\Api\Data\QueueInterfaceFactory;
use Borntechies\Import\Model\ResourceModel\Queue as QueueResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DB\QueryBuilderFactory;

/**
 * Class QueueRepository
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class QueueRepository implements QueueRepositoryInterface
{
    /**
     * @var QueueInterfaceFactory
     */
    protected $queueFactory;

    /**
     * @var QueueResource
     */
    protected $resource;

    /**
     * @var QueueCollectionInterfaceFactory
     */
    protected $queueCollectionFactory;

    /**
     * @var QueryBuilderFactory
     */
    protected $queryBuilderFactory;

    /**
     * @param QueueInterfaceFactory $queueFactory
     * @param Queue $resource
     * @param QueueCollectionInterfaceFactory $queueCollectionFactory
     * @param QueryBuilderFactory $queryBuilderFactory
     */
    public function __construct(
        QueueInterfaceFactory $queueFactory,
        QueueResource $resource,
        QueueCollectionInterfaceFactory $queueCollectionFactory,
        QueryBuilderFactory $queryBuilderFactory
    ) {
        $this->queueFactory = $queueFactory;
        $this->resource = $resource;
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QueueInterface $queue)
    {
        try {
            $this->resource->save($queue);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the queue: %1', $exception->getMessage()));
        }

        return $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        /** @var $queue QueueInterface */
        $queue = $this->queueFactory->create();
        $this->resource->load($queue, $id);
        if (!$queue->getId()) {
            throw new NoSuchEntityException(__('Queue with id "%1" does not exist.', $id));
        }

        return $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(QueueCriteriaInterface $searchCriteria)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder->setCriteria($searchCriteria);
        $queryBuilder->setResource($this->resource);
        $query = $queryBuilder->create();
        $collection = $this->queueCollectionFactory->create(['query' => $query]);

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(QueueInterface $queue)
    {
        try {
            $this->resource->delete($queue);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete the queue: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($queueId)
    {
        return $this->delete($this->get($queueId));
    }

}
