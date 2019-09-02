<?php
namespace Borntechies\Import\Controller\Adminhtml\Queue;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Borntechies\Import\Api\QueueRepositoryInterface;

/**
 * Class View
 * @author      Anil <anil.shah@borntechies.com>
 */
class View extends Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var QueueRepositoryInterface
     */
    protected $queueRepository;

    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param QueueRepositoryInterface $queue
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        QueueRepositoryInterface $queue
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->queueRepository = $queue;
    }

    /**
     * Init actions
     *
     * @return Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Borntechies_Import::Queue')
            ->addBreadcrumb(__('Catalog & Customers Import'), __('Catalog & Customers Import'));

        return $resultPage;
    }

    /**
     * View Queue item
     *
     * @return Page|Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $queue = $this->queueRepository->get($id);
                $this->coreRegistry->register('queue_item', $queue);

                /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
                $resultPage = $this->_initAction();
                $resultPage->getConfig()->getTitle()
                    ->prepend(__('%1 Import (%2)', $queue->getTransactionTypeText(), $queue->getCreatedAt()));

                return $resultPage;
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while loading the item.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('queue/index/');

                return $resultRedirect;
            }
        }

        $this->messageManager->addErrorMessage( __('ID parameter is missed.'));
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('queue/index/');

        return $resultRedirect;
    }
}