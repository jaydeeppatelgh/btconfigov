<?php
namespace Borntechies\LicensePlate\Model;

use Borntechies\LicensePlate\Api\Data;
use Borntechies\LicensePlate\Api\ModelRepositoryInterface;
use Borntechies\LicensePlate\Api\Data\ModelInterfaceFactory;
use Borntechies\LicensePlate\Model\ResourceModel\Model as ModelResource;
use Borntechies\LicensePlate\Model\ResourceModel\Model\CollectionFactory as ModelCollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SortOrder;

/**
 * Class ModelRepository
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class ModelRepository implements  ModelRepositoryInterface
{
    /**
     * @var ModelFactory
     */
    protected $modelFactory;

    /**
     * @var ModelResource
     */
    protected $resource;

    /**
     * @var ModelCollectionFactory
     */
    protected $modelCollectionFactory;

    /**
     * @var Data\ModelSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * ModelRepository constructor.
     *
     * @param ModelFactory            $modelFactory
     * @param ModelResource                    $resource
     * @param ModelCollectionFactory           $modelCollectionFactory
     * @param Data\ModelSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ModelFactory $modelFactory,
        ModelResource $resource,
        ModelCollectionFactory $modelCollectionFactory,
        Data\ModelSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->modelFactory = $modelFactory;
        $this->resource = $resource;
        $this->modelCollectionFactory = $modelCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Data\ModelInterface $model)
    {
        try {
            $this->resource->save($model);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the model: %1',
                $exception->getMessage()
            ));
        }
        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function get($modelId)
    {
        $model = $this->modelFactory->create();
        $this->resource->load($model, $modelId);
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('Model with id "%1" does not exist.', $modelId));
        }
        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->modelCollectionFactory->create();
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
    public function delete(Data\ModelInterface $model)
    {
        try {
            $this->resource->delete($model);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the model: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($modelId)
    {
        return $this->delete($this->get($modelId));
    }

}