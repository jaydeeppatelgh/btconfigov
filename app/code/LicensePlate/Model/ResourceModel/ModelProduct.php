<?php
namespace Borntechies\LicensePlate\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ModelProduct
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class ModelProduct extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('license_plate_product', 'id');
    }
}