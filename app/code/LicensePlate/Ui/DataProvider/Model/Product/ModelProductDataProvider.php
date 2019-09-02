<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Ui\DataProvider\Product\Related;

use Borntechies\LicensePlate\Api\Data\ModelInterface;
use Borntechies\LicensePlate\Api\Data\ModelProductInterface;
use Borntechies\LicensePlate\Api\ModelProductRepositoryInterface;
use Borntechies\LicensePlate\Api\ModelRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Class ModelProductDataProvider
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class ModelProductDataProvider extends ProductDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var ModelProductRepositoryInterface
     */
    protected $modelProductRepository;

    /**
     * @var ModelInterface
     */
    private $model;

    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @var ModelRepositoryInterface
     */
    private $modelRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param ProductRepositoryInterface $productRepository
     * @param StoreRepositoryInterface $storeRepository
     * @param ModelProductRepositoryInterface $modelProductRepository
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        ProductRepositoryInterface $productRepository,
        StoreRepositoryInterface $storeRepository,
        ModelProductRepositoryInterface $modelProductRepository,
        ModelRepositoryInterface $modelRepositoryInterface,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        $addFieldStrategies,
        $addFilterStrategies,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $collectionFactory,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data
        );

        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->storeRepository = $storeRepository;
        $this->modelProductRepository = $modelProductRepository;
        $this->modelRepository = $modelRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        /** @var Collection $collection */
        $collection = parent::getCollection();
        $collection->addAttributeToSelect('status');

        if ($this->getStore()) {
            $collection->setStore($this->getStore());
        }

        if (!$this->getProduct()) {
            return $collection;
        }

        $collection->addAttributeToFilter(
            $collection->getIdFieldName(),
            ['nin' => [$this->getProduct()->getId()]]
        );

        return $this->addCollectionFilters($collection);
    }

    /**
     * Add specific filters
     *
     * @param Collection $collection
     * @return Collection
     */
    protected function addCollectionFilters(Collection $collection)
    {
        $products = [];
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('model_id', $this->getModel()->getId())->create();
        /** @var ModelProductInterface $linkItem */
        foreach ($this->modelProductRepository->getList($searchCriteria) as $linkItem) {
            $products[] = $this->productRepository->get($linkItem->getProductId())->getId();
        }

        if ($products) {
            $collection->addAttributeToFilter(
                $collection->getIdFieldName(),
                ['nin' => [$products]]
            );
        }

        return $collection;
    }

    /**
     * Retrieve product
     *
     * @return ProductInterface|null
     */
    protected function getModel()
    {
        if (null !== $this->model) {
            return $this->model;
        }

        if (!($id = $this->request->getParam('current_model_id'))) {
            return null;
        }

        return $this->model = $this->modelRepository->get($id);
    }

    /**
     * Retrieve store
     *
     * @return StoreInterface|null
     */
    protected function getStore()
    {
        if (null !== $this->store) {
            return $this->store;
        }

        if (!($storeId = $this->request->getParam('current_store_id'))) {
            return null;
        }

        return $this->store = $this->storeRepository->getById($storeId);
    }
}
