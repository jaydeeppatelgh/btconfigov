<?php
namespace Borntechies\Import\Model;

use Borntechies\Import\Api\Data\QueueInterface;
use Magento\Framework\Model\AbstractModel;
use Borntechies\Import\Model\Import;
use Borntechies\Import\Helper\Data as BorntechiesHelper;

/**
 * Class Queue
 *
 * @author      Anil <anil.shah@borntechies.com>
 *
 * @method ResourceModel\Queue getResource()
 * @method ResourceModel\Queue _getResource()
 */
class Queue extends AbstractModel implements QueueInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'queue';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Borntechies\Import\Model\ResourceModel\Queue::class);
    }

    /**
     * Get all available statuses
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            BorntechiesHelper::STATUS_SCHEDULED => __('Scheduled'),
            BorntechiesHelper::STATUS_RUNNING   => __('Running'),
            BorntechiesHelper::STATUS_SUCCESS   => __('Success'),
            BorntechiesHelper::STATUS_ERROR     => __('Error')
        ];
    }

    /**
     * Get all available transmission types
     *
     * @return array
     */
    public function getAvailableTransactionTypes()
    {
        return array(
            BorntechiesHelper::TYPE_CUSTOMER  => __('Customer'),
            BorntechiesHelper::TYPE_PRODUCT   => __('Product'),
            BorntechiesHelper::TYPE_PRICE     =>  __('Prices'),
            BorntechiesHelper::TYPE_UPSELL    =>  __('Upsell Products'),
            BorntechiesHelper::TYPE_CATEGORY  =>  __('Categories'),
            BorntechiesHelper::TYPE_LICENSE_PLATE => __('License Plates'),
            BorntechiesHelper::TYPE_STOCK     => __('Stock')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionTypeText()
    {
        foreach ($this->getAvailableTransactionTypes() as $key => $value) {
            if ($this->getTransactionType() == $key) {
                return __($value);
            }
        }

        return $this->getTransactionType();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusText()
    {
        foreach ($this->getAvailableStatuses() as $key => $value) {
            if ($this->getStatus() == $key) {
                return __($value);
            }
        }

        return $this->getStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactionType()
    {
        return $this->getData(self::TRANSACTION_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTransactionType($type)
    {
        $this->setData(self::TRANSACTION_TYPE, $type);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        $this->setData(self::MESSAGE, $message);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($time)
    {
        $this->setData(self::CREATED_AT, $time);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessedAt()
    {
        return $this->getData(self::PROCESSED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setProcessedAt($time)
    {
        $this->setData(self::PROCESSED_AT, $time);
        return $this;
    }
}