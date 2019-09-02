<?php
namespace Borntechies\LicensePlate\Controller\Adminhtml\Model;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Index extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Borntechies_LicensePlate::license_plate';

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
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Borntechies_LicensePlate::license_plate');
        $resultPage->addBreadcrumb(__('License Plate'), __('License Plate'));
        $resultPage->addBreadcrumb(__('Manage Models'), __('Manage Models'));
        $resultPage->getConfig()->getTitle()->prepend(__('Models'));

        return $resultPage;
    }
}