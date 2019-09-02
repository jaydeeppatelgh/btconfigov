<?php
namespace Borntechies\LicensePlate\Model\ResourceModel\Model;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Borntechies\LicensePlate\Model\Model::class, \Borntechies\LicensePlate\Model\ResourceModel\Model::class);
    }

    /**
     * Filter models by product id
     *
     * @param int $productId
     *
     * @return $this
     */
    public function addProductLimitation($productId)
    {
        $this->getSelect()
            ->joinLeft(['products' =>  $this->getConnection()->getTableName('license_plate_product')], 'main_table.id = products.model_id', [])
            ->where('products.product_id = ?', $productId)
            ->group('main_table.id');

        return $this;
    }
}