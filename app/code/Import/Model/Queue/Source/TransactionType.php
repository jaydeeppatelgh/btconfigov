<?php
namespace Borntechies\Import\Model\Queue\Source;

use \Magento\Framework\Data\OptionSourceInterface;
use \Borntechies\Import\Model\Queue as Queue;

/**
 * Class TransactionType
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class TransactionType implements OptionSourceInterface
{
    /**
     * @var Queue
     */
    protected $queue;

    /**
     * Constructor
     *
     * @param Queue $queue
     */
    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->queue->getAvailableTransactionTypes();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }

        return $options;
    }
}