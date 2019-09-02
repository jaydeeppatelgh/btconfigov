<?php
namespace Borntechies\Import\Model\ResourceModel\Queue;

use Magento\Framework\Data\AbstractCriteria;
use Borntechies\Import\Api\QueueCriteriaInterface;
use Borntechies\Import\Api\Data\QueueInterface;

/**
 * Class Criteria
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Criteria extends AbstractCriteria implements QueueCriteriaInterface
{
    /**
     * Constructor
     *
     * @param string $mapper
     */
    public function __construct($mapper = '')
    {
        $this->mapperInterfaceName = $mapper ?: \Borntechies\Import\Model\ResourceModel\Queue\CriteriaMapper::class;
        $this->data['initial_condition'] = true;
    }

    /**
     * Add condition to find items that were created more than $date ago
     *
     * @param string $date
     *
     * @return $this
     * @throws \Exception
     */
    public function addCleanCondition($date)
    {
        $this->addFilter(QueueInterface::CREATED_AT, QueueInterface::CREATED_AT, ["lt" => $date], 'public');

        return $this;
    }
}