<?php
namespace Borntechies\LicensePlate\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ModelProductInterface
 * @api
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
interface ModelProductInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const MODEL_ID      = 'model_id';
    const PRODUCT_ID    = 'product_id';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function  getId();

    /**
     * Get Model ID
     *
     * @return string
     */
    public function getModelId();

    /**
     * Get product id
     *
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product id
     *
     * @param int $id
     *
     * @return ModelProductInterface
     */
    public function setProductId($id);

    /**
     * Set model id
     *
     * @param int $id
     *
     * @return ModelProductInterface
     */
    public function setModelId($id);
}