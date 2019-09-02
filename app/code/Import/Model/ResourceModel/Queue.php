<?php
namespace Borntechies\Import\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Borntechies\Import\Api\Data\QueueInterface;

/**
 * Class Queue
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Queue extends AbstractDb
{
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('queue_manager', QueueInterface::QUEUE_ID);
    }
}