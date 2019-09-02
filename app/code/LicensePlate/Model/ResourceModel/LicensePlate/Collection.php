<?php
namespace Borntechies\LicensePlate\Model\ResourceModel\LicensePlate;

use Borntechies\LicensePlate\Api\ModelProductRepositoryInterface;
use Borntechies\LicensePlate\Api\Data\ModelInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\Product\Visibility;

/**
 * Class Collection
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Collection extends ProductCollection
{
    /**
     * @param ModelInterface $model
     *
     * @return $this
     */
    public function addSearchByModel(ModelInterface $model)
    {
        $this->addFieldToFilter('entity_id', ['in' => $this->getModelProducts($model)]);
        return $this;
    }

    /**
     * Get product ids for current model
     *
     * @param ModelInterface $model
     *
     * @return array
     */
    private function getModelProducts(ModelInterface $model)
    {
        $ids = [];
        try {
            /**@var ModelProductRepositoryInterface $productRepository */
            $productRepository = \Magento\Framework\App\ObjectManager::getInstance()
                ->create(ModelProductRepositoryInterface::class);

            $products = $productRepository->getModelProducts($model);

            foreach ($products->getItems() as $item) {
                $ids[] = $item->getProductId();
            }
        } catch (LocalizedException $e) {
            return [];
        }
        return $ids;

    }

    /**
     * Count qty of products in the specific categories
     * Return array of category => product qty
     *
     * @param \Magento\Catalog\Model\Category[] $categories
     *
     * @return array
     */
    public function getCategoryFilterOptions($categories)
    {
        $ids = $this->getAllIds();
        $child = [];
        foreach ($categories as $category) {
            $child[] = $category->getId();
        }

        $select = $this->getConnection()->select()
            ->from('catalog_category_product_index', ['category_id', 'count' => new \Zend_Db_Expr('COUNT(*)')])
            ->group('category_id')
            ->where('store_id = ?', $this->getStoreId())
            ->where('product_id in (?)', $ids)
            ->where('visibility in (?)', [Visibility::VISIBILITY_BOTH, Visibility::VISIBILITY_IN_SEARCH])
            ->where('category_id in (?)', $child);

        return $this->getConnection()->fetchPairs($select);
    }
}