<?php
namespace Borntechies\LicensePlate\Model;

use Borntechies\LicensePlate\Model\ResourceModel\ModelProduct as ModelProductResource;
use Borntechies\LicensePlate\Model\ResourceModel\ModelProduct\CollectionFactory as ModelProductCollectionFactory;
use Borntechies\LicensePlate\Api\Data;
use Borntechies\LicensePlate\Api\ModelProductRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SortOrder;

/**
 * Class ModelProductRepository
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class ModelProductRepository implements ModelProductRepositoryInterface
{
    /**
     * @var ModelProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var ModelProductResource
     */
    protected $resource;

    /**
     * @var Data\ModelProductSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * ModelProductRepository constructor.
     *
     * @param ModelProductCollectionFactory                  $productCollectionFactory
     * @param ModelProductResource                           $resource
     * @param Data\ModelProductSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ModelProductCollectionFactory $productCollectionFactory,
        ModelProductResource $resource,
        Data\ModelProductSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->resource = $resource;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Data\ModelProductInterface $modelProduct)
    {
        try {
            $this->resource->save($modelProduct);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the model product link: %1',
                $exception->getMessage()
            ));
        }
        return $modelProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->productCollectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();

        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Data\ModelProductInterface $modelProduct)
    {
        try {
            $this->resource->delete($modelProduct);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the model product: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($modelId, $productId)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter(Data\ModelProductInterface::MODEL_ID, $modelId);
        $collection->addFieldToFilter(Data\ModelProductInterface::PRODUCT_ID, $productId);
        $productLink = $collection->getFirstItem();
        if (!$productLink->getId()) {
            throw new NoSuchEntityException(__('Product id %2 is not linked with model %1', $modelId, $productId));
        }

        return $productLink;
    }

    /**
     * {@inheritdoc}
     */
    public function getModelProducts(Data\ModelInterface $model)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter(Data\ModelProductInterface::MODEL_ID, $model->getId());

        return $collection->load();
    }

}
