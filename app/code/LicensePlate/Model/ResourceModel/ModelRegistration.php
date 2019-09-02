<?php
namespace Borntechies\LicensePlate\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ModelRegistration
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class ModelRegistration extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('license_plate_registration', 'id');
    }
}