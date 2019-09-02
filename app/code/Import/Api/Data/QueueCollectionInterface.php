<?php
namespace Borntechies\Import\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface CompanyCollectionInterface
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
interface QueueCollectionInterface extends SearchResultsInterface
{
    /**
     * Get items
     *
     * @return QueueInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param QueueInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);

}