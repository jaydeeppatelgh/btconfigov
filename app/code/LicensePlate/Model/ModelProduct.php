<?php
namespace Borntechies\LicensePlate\Model;

use Magento\Framework\Model\AbstractModel;
use Borntechies\LicensePlate\Api\Data\ModelProductInterface;

/**
 * Class ModelProduct
 * @method ResourceModel\ModelProduct _getResource()
 * @method ResourceModel\ModelProduct getResource()
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class ModelProduct extends AbstractModel implements ModelProductInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Borntechies\LicensePlate\Model\ResourceModel\ModelProduct::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getModelId()
    {
        return $this->getData(self::MODEL_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($id)
    {
        return $this->setData(self::PRODUCT_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function setModelId($id)
    {
        return $this->setData(self::MODEL_ID, $id);
    }
}