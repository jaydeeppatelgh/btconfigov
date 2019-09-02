<?php
namespace Borntechies\Import\Controller\Adminhtml\Queue;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Page;

/**
 * Class Index
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Borntechies_Import::Queue');
        $resultPage->addBreadcrumb(__('Catalog & Customers Import'), __('Catalog & Customers Import'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import Manager Queue'));

        return $resultPage;
    }
}