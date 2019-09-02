<?php
namespace Borntechies\LicensePlate\Model;

use Borntechies\LicensePlate\Api\Data\ModelRegistrationInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class ModelRegistration
 * @method ResourceModel\ModelRegistration _getResource()
 * @method ResourceModel\ModelRegistration getResource()
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class ModelRegistration extends AbstractModel implements ModelRegistrationInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Borntechies\LicensePlate\Model\ResourceModel\ModelRegistration::class);
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
    public function getRegistration()
    {
        return $this->getData(self::REGISTRATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setRegistration($registration)
    {
        return $this->setData(self::REGISTRATION, $registration);
    }

    /**
     * {@inheritdoc}
     */
    public function setModelId($id)
    {
        return $this->setData(self::MODEL_ID, $id);
    }
}