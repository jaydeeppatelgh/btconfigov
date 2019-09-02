<?php
namespace Borntechies\LicensePlate\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class LicensePlate
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class LicensePlate extends AbstractDb
{
    /**
     * Initialize connection and define catalog product table as main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('catalog_product_entity', 'entity_id');
    }
}