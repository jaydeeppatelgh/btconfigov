<?php
namespace Borntechies\LicensePlate\Block\Category\Product;

use Magento\Framework\View\Element\Template;
use Borntechies\LicensePlate\Model\ResourceModel\Model\CollectionFactory;

/**
 * Class Parts
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Parts extends Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        CollectionFactory $collectionFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get models by product
     *
     * @return bool|\Borntechies\LicensePlate\Model\ResourceModel\Model\Collection
     */
    public function getProductParts()
    {
        $product = $this->registry->registry('current_product');

        if ($product) {
            $collection = $this->collectionFactory->create();
            $collection->addProductLimitation($product->getId());
            $collection->setOrder('make','ASC');
            $collection->setOrder('model','ASC');
            $collection->setOrder('fuel','ASC');
            $collection->setOrder('motor_code','ASC');
            $collection->setOrder('construction_period','ASC');
            return $collection;
        }

        return false;
    }
}