<?php
namespace Borntechies\Import\Model\ResourceModel\Queue;

use Magento\Framework\Data\AbstractSearchResult;
use Borntechies\Import\Api\Data\QueueCollectionInterface;

/**
 * Class Collection
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Collection extends AbstractSearchResult implements QueueCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->setDataInterfaceName(\Borntechies\Import\Api\Data\QueueInterface::class);
    }
}