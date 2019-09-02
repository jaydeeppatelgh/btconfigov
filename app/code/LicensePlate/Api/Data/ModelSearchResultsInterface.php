<?php
namespace Borntechies\LicensePlate\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ModelSearchResultsInterface
 * @api
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
interface ModelSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get models list.
     *
     * @return ModelInterface[]
     */
    public function getItems();

    /**
     * Set model list.
     *
     * @param ModelInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}