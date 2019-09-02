<?php
namespace Borntechies\Import\Model\ResourceModel\Queue;

use Magento\Framework\DB\GenericMapper;

/**
 * Class CriteriaMapper
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class CriteriaMapper extends GenericMapper
{
    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->initResource(\Borntechies\Import\Model\ResourceModel\Queue::class);
    }
}