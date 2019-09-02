<?php
namespace Borntechies\LicensePlate\Model\ResourceModel\ModelRegistration;

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
        $this->_init(\Borntechies\LicensePlate\Model\ModelRegistration::class, \Borntechies\LicensePlate\Model\ResourceModel\ModelRegistration::class);
    }

    /**
     * @param int $modelId
     *
     * @return \Borntechies\LicensePlate\Model\ModelRegistration[]
     */
    public function getModelRegistration($modelId)
    {
        $collection = $this->addFieldToFilter(
            'main_table.model_id',
            $modelId
        )->setOrder(
            'id',
            'asc'
        );

        return $collection->getItems();
    }
}