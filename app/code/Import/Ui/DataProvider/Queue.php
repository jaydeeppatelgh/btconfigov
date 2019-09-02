<?php
namespace Borntechies\Import\Ui\DataProvider;

use Borntechies\Import\Api\Data\QueueInterface;
use Magento\Framework\Registry;
use Borntechies\Import\Api\QueueRepositoryInterface;
use Borntechies\Import\Api\QueueCriteriaInterface;
use Borntechies\Import\Helper\Data as BorntechiesHelper;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class Queue
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Queue extends AbstractDataProvider
{
    /**
     * @var QueueRepositoryInterface
     */
    protected $queueRepository;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var QueueCriteriaInterface
     */
    protected $queueCriteria;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param QueueRepositoryInterface $repositoryInterface
     * @param QueueCriteriaInterface $criteriaInterface
     * @param Registry $registry
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        QueueRepositoryInterface $repositoryInterface,
        QueueCriteriaInterface $criteriaInterface,
        Registry $registry,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->queueRepository = $repositoryInterface;
        $this->queueCriteria = $criteriaInterface;
        $this->coreRegistry = $registry;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $this->collection = $this->queueRepository->getList($this->queueCriteria);

        /** @var QueueInterface $item */
        foreach ($this->collection->getItems() as $item) {
            $result['queue'] = $item->getData();
            $result['queue']['message'] = nl2br($item->getMessage());
            $result['queue']['status'] = $this->getItemStatus($item);
            $result['queue']['transaction_type'] = $item->getTransactionTypeText();

            $this->loadedData[$item->getId()] = $result;
        }

        return $this->loadedData;
    }

    /**
     * @param QueueInterface $item
     *
     * @return string
     */
    protected function getItemStatus(QueueInterface $item)
    {
        $class = '';

        switch ($item->getStatus()) {
            case BorntechiesHelper::STATUS_ERROR    : $class = 'error'; break;
            case BorntechiesHelper::STATUS_SUCCESS  : $class = 'success'; break;
            case BorntechiesHelper::STATUS_RUNNING  :
            case BorntechiesHelper::STATUS_SCHEDULED: $class = 'process';
        }

        return sprintf('<span class="%s">%s</span>', $class, $item->getStatusText());
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->queueCriteria->addFilter($filter->getField(), $filter->getField(), $filter->getValue(), $filter->getConditionType());
    }
}