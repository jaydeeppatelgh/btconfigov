<?php
namespace Borntechies\Import\Model\Queue;

use Borntechies\Import\Api\QueueManagementInterface;
use Borntechies\Import\Api\Data\QueueInterface;
use Borntechies\Import\Api\QueueRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Borntechies\Import\Helper\Data as BorntechiesHelper;

/**
 * Class Management
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Management implements QueueManagementInterface
{
    /**
     * @var QueueInterface
     */
    protected $queue;

    /**
     * @var QueueRepositoryInterface
     */
    protected $queueRepository;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @param QueueRepositoryInterface $queueRepository
     * @param QueueInterface $queue
     * @param DateTime $date
     */
    public function __construct(
        QueueRepositoryInterface $queueRepository,
        QueueInterface $queue,
        DateTime $date
    ) {
        $this->queueRepository = $queueRepository;
        $this->queue = $queue;
        $this->date = $date;
    }

    /**
     * Add Queue Report
     *
     * @param string $type
     * @param null|string $status
     *
     * @return QueueInterface
     */
    public function addReport($type, $status = null)
    {
        if (!$status) {
            $status = BorntechiesHelper::STATUS_RUNNING;
        }
        $this->queue->setStatus($status)
            ->setTransactionType($type)
            ->setCreatedAt($this->date->date());
       return  $this->queueRepository->save($this->queue);
    }


    /**
     * Update queue report
     *
     * @param string $updateSummary
     * @param null|string $status
     *
     * @return QueueInterface
     */
    public function updateReport($updateSummary, $status = null)
    {
        if (!$status) {
            $status = BorntechiesHelper::STATUS_SUCCESS;
        }
        $this->queue->setProcessedAt($this->date->date())
            ->setMessage($updateSummary)
            ->setStatus($status);

       return $this->queueRepository->save($this->queue);
    }

    /**
     * Mark queue report as invalid
     *
     * @param string $updateSummary
     *
     * @return QueueInterface
     */
    public function invalidateReport($updateSummary)
    {
        $this->queue->setProcessedAt($this->date->date())
            ->setMessage($updateSummary)
            ->setStatus(BorntechiesHelper::STATUS_ERROR);

        return $this->queueRepository->save($this->queue);
    }
}