<?php
namespace Borntechies\Import\Api\Data;

/**
 * Interface QueueInterface
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
interface QueueInterface
{
    /**
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const QUEUE_ID = 'id';
    const TRANSACTION_TYPE = 'transaction_type';
    const STATUS = 'status';
    const MESSAGE = 'message';
    const CREATED_AT = 'created_at';
    const PROCESSED_AT = 'processed_at';

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set id
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Get transaction type
     *
     * @return int|null
     */
    public function getTransactionType();

    /**
     * Set transaction type
     *
     * @param string $type
     *
     * @return $this
     */
    public function setTransactionType($type);

    /**
     * Get status
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get message
     *
     * @return string|null
     */
    public function getMessage();

    /**
     * Set message
     *
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message);

    /**
     * Get created time
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created time
     *
     * @param string $time
     *
     * @return $this
     */
    public function setCreatedAt($time);

    /**
     * Get processed time
     *
     * @return string|null
     */
    public function getProcessedAt();

    /**processed time
     *
     * @param string $time
     *
     * @return $this
     */
    public function setProcessedAt($time);

    /**
     * Get translated status
     *
     * @return \Magento\Framework\Phrase
     */
    public function getStatusText();

    /**
     * Get translated label for current transmission type
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTransactionTypeText();
}