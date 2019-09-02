<?php
namespace Borntechies\Import\Cron;

use Borntechies\Import\Api\QueueRepositoryInterface;
use Borntechies\Import\Api\QueueCriteriaInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

/**
 * Class CleanQueue
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class CleanQueue
{
    const PATH_KEEP_LOGS    = 'queue_manager/general/keep_logs';
    const DEFAULT_KEEP_LOGS = 30;

    /**
     * @var QueueRepositoryInterface
     */
    protected $queueRepository;

    /**
     * @var QueueCriteriaInterface
     */
    protected $queueCriteria;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @param QueueRepositoryInterface $repositoryInterface
     * @param QueueCriteriaInterface $criteriaInterface
     * @param ScopeConfigInterface $configInterface
     * @param DateTime $localeDate
     * @param LoggerInterface $logger
     */
    public function __construct(
        QueueRepositoryInterface $repositoryInterface,
        QueueCriteriaInterface $criteriaInterface,
        ScopeConfigInterface $configInterface,
        DateTime $localeDate,
        LoggerInterface $logger
    ) {
        $this->queueCriteria = $criteriaInterface;
        $this->queueRepository = $repositoryInterface;
        $this->scopeConfig = $configInterface;
        $this->date = $localeDate;
        $this->logger = $logger;
    }

    /**
     * Clean old queue items from the database
     *
     * @return void
     */
    public function execute()
    {
        $keepDays = $this->scopeConfig->getValue(self::PATH_KEEP_LOGS);
        if (!$keepDays) {
            $keepDays = self::DEFAULT_KEEP_LOGS;
        }
        $date = \DateTime::createFromFormat(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT, $this->date->date())
            ->modify("-{$keepDays} day")
            ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

        try {
            $this->queueCriteria->addCleanCondition($date);
            $collection = $this->queueRepository->getList($this->queueCriteria);
            foreach ($collection->getItems() as $item) {
                $this->queueRepository->delete($item);
            }
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
        }
    }
}