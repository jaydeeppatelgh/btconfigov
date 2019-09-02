<?php
namespace Borntechies\Catalog\Model\Indexer\Category\Product;

use Magento\Catalog\Model\Indexer\Category\Product\Action\Full as FullReindex;

/**
 * Class Full
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Full extends FullReindex
{
    /**
     * Check whether select ranging is needed
     * Fix issue https://github.com/magento/magento2/issues/8018
     *
     * @return bool
     */
    protected function isRangingNeeded()
    {
        return false;
    }
}