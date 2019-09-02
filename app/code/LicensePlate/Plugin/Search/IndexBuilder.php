<?php
namespace  Borntechies\LicensePlate\Plugin\Search;

use \Magento\Framework\Registry;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResourceConnection;

/**
 * Class IndexBuilder
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class IndexBuilder
{
    /**
     * @var Resource
     */
    private $resource;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * IndexBuilder constructor.
     *
     * @param Session $session
     * @param ResourceConnection $resource
     * @param Registry $registry
     */
    public function __construct(
        Session $session,
        ResourceConnection $resource,
        Registry $registry
    ) {
        $this->customerSession = $session;
        $this->resource = $resource;
        $this->registry = $registry;
    }

    /**
     * Add filter to collection if model is selected
     *
     * @param \Magento\CatalogSearch\Model\Search\IndexBuilder $subject
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $select
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function afterBuild(\Magento\CatalogSearch\Model\Search\IndexBuilder $subject, $select)
    {
        if (!$this->registry->registry('current_category') && $this->customerSession->getCurrentLicensePlateModel()) {
            $licensePlateId = $this->customerSession->getCurrentLicensePlateModel()->getId();
            $joinTableName = $this->resource->getConnection()->getTableName('license_plate_product');
            $select->joinInner(
                $joinTableName,
                "search_index.entity_id = {$joinTableName}.product_id AND {$joinTableName}.model_id = {$licensePlateId}",
                []);
        }

        return $select;
    }
}