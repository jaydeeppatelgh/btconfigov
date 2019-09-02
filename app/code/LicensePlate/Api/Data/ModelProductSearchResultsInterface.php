<?php
namespace Borntechies\LicensePlate\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ModelSearchResultsInterface
 * @api
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
interface ModelProductSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get models list.
     *
     * @return ModelProductInterface[]
     */
    public function getItems();

    /**
     * Set model list.
     *
     * @param ModelProductInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}