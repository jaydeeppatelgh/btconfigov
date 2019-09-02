<?php
namespace Borntechies\LicensePlate\Model\ResourceModel\ModelProduct;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Collection extends AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Borntechies\LicensePlate\Model\ModelProduct::class, \Borntechies\LicensePlate\Model\ResourceModel\ModelProduct::class);
    }
}