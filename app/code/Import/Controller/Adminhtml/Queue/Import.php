<?php
namespace Borntechies\Import\Controller\Adminhtml\Queue;

use Borntechies\Import\Helper\Data as BorntechiesHelper;
use Borntechies\Import\Api\QueueManagementInterface;
use Borntechies\Import\Model\Import\Product as ProductImport;
use Borntechies\Import\Model\Import\Customer as CustomerImport;
use Borntechies\Import\Model\Import\Price as PriceImport;
use Borntechies\Import\Model\Import\Category as CategoryImport;
use Borntechies\Import\Model\Import\Upsell as UpsellImport;
use Borntechies\Import\Model\Import\Stock as StockImport;
use Magento\Indexer\Model\IndexerFactory;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\ImportExport\Controller\Adminhtml\ImportResult as ImportResultController;
use Magento\Framework\Controller\ResultFactory;
use Magento\ImportExport\Model\Report\ReportProcessorInterface;
use Magento\ImportExport\Model\History;
use Magento\ImportExport\Helper\Report;


/**
 * Class Import
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Import extends ImportResultController
{
    /**
     * @var ProductImport
     */
    protected $productImport;

    /**
     * @var QueueManagementInterface
     */
    protected $queueManagement;

    /**
     * @var CustomerImport
     */
    protected $customerImport;

    /**
     * @var IndexerFactory
     */
    protected $indexerFactory;

    /**
     * @var CategoryImport
     */
    protected $categoryImport;

    /**
     * @var UpsellImport
     */
    protected $upsellImport;

    /**
     * @var PriceImport
     */
    protected $priceImport;

    /**
     * @var StockImport
     */
    protected $stockImport;

    /**
     * @param Context $context
     * @param ReportProcessorInterface $reportProcessor
     * @param History $historyModel
     * @param Report $reportHelper
     * @param ProductImport $productImport
     * @param CustomerImport $customerImport
     * @param QueueManagementInterface $queueManagement
     * @param IndexerFactory $indexerFactory
     * @param PriceImport $price
     * @param CategoryImport $category
     * @param UpsellImport $upsell
     * @param StockImport $stock
     */
    public function __construct(
        Context $context,
        ReportProcessorInterface $reportProcessor,
        History $historyModel,
        Report $reportHelper,
        ProductImport $productImport,
        CustomerImport $customerImport,
        QueueManagementInterface $queueManagement,
        IndexerFactory $indexerFactory,
        PriceImport $price,
        CategoryImport $category,
        UpsellImport $upsell,
        StockImport $stock
    ) {
        parent::__construct($context, $reportProcessor, $historyModel, $reportHelper);

        $this->productImport = $productImport;
        $this->queueManagement = $queueManagement;
        $this->customerImport = $customerImport;
        $this->indexerFactory = $indexerFactory;
        $this->priceImport = $price;
        $this->categoryImport = $category;
        $this->upsellImport = $upsell;
        $this->stockImport = $stock;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $type = $this->getRequest()->getParam('type');

        switch ($type) {
            case BorntechiesHelper::TYPE_PRODUCT:
                try{
                    $this->queueManagement->addReport(BorntechiesHelper::TYPE_PRODUCT);
                    $this->productImport->import();
                    $this->queueManagement->updateReport($this->productImport->getMessage(), BorntechiesHelper::STATUS_SUCCESS);
                } catch (\Magento\Framework\Exception\CouldNotSaveException $e) {
                    $this->messageManager->addErrorMessage(__('Could not generate report'));
                } catch (\Exception $e) {
                    $message = $this->productImport->getMessage().PHP_EOL.$e->getMessage();
                    $this->queueManagement->invalidateReport($message);
                }

                break;
            case BorntechiesHelper::TYPE_CUSTOMER:
                try {
                    $this->queueManagement->addReport(BorntechiesHelper::TYPE_CUSTOMER);
                    $this->customerImport->import();
                    $this->queueManagement->updateReport($this->customerImport->getFormatedLogTrace(), BorntechiesHelper::STATUS_SUCCESS);
                    $indexer = $this->indexerFactory->create();
                    $indexer->load("customer_grid");
                    $indexer->reindexAll();
                } catch (\Exception $e) {
                    $message = $this->customerImport->getFormatedLogTrace().PHP_EOL.$e->getMessage();
                    $this->queueManagement->invalidateReport($message);
                }
                break;
            case BorntechiesHelper::TYPE_UPSELL;
                try {
                    $this->queueManagement->addReport(BorntechiesHelper::TYPE_UPSELL);
                    $this->upsellImport->import();
                    $this->queueManagement->updateReport($this->upsellImport->getMessage(), BorntechiesHelper::STATUS_SUCCESS);
                } catch (\Exception $e) {
                    $message = $this->upsellImport->getMessage().PHP_EOL.$e->getMessage();
                    $this->queueManagement->invalidateReport($message);
                }
                break;
            case BorntechiesHelper::TYPE_PRICE:
                try {
                    $this->queueManagement->addReport(BorntechiesHelper::TYPE_PRICE);
                    $this->priceImport->import();
                    $this->queueManagement->updateReport($this->priceImport->getMessage(), BorntechiesHelper::STATUS_SUCCESS);
                } catch (\Exception $e) {
                    $message = $this->priceImport->getMessage().PHP_EOL.$e->getMessage();
                    $this->queueManagement->invalidateReport($message);
                }
                break;
            case BorntechiesHelper::TYPE_CATEGORY:
                try {
                    $this->queueManagement->addReport(BorntechiesHelper::TYPE_CATEGORY);
                    $this->categoryImport->import();
                    $this->queueManagement->updateReport($this->categoryImport->getMessage(), BorntechiesHelper::STATUS_SUCCESS);
                } catch (\Exception $e) {
                    $message = $this->categoryImport->getMessage().PHP_EOL.$e->getMessage();
                    $this->queueManagement->invalidateReport($message);
                }
                break;
            case BorntechiesHelper::TYPE_STOCK:
                try {
                    $this->queueManagement->addReport(BorntechiesHelper::TYPE_STOCK);
                    $this->stockImport->import();
                    $this->queueManagement->updateReport($this->stockImport->getMessage(), BorntechiesHelper::STATUS_SUCCESS);
                } catch (\Exception $e) {
                    $message = $this->stockImport->getMessage().PHP_EOL.$e->getMessage();
                    $this->queueManagement->invalidateReport($message);
                }
                break;
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;

    }
}