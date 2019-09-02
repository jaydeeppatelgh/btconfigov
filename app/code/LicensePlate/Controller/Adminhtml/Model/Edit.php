<?php
namespace Borntechies\LicensePlate\Controller\Adminhtml\Model;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Borntechies\LicensePlate\Api\ModelRepositoryInterface;

/**
 * Class Edit
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Borntechies_LicensePlate::license_plate';

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
     * @var ModelRepositoryInterface
     */
    protected $modelRepository;

    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        ModelRepositoryInterface $modelRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->modelRepository = $modelRepository;
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Borntechies_LicensePlate::license_plate')
            ->addBreadcrumb(__('License Plate'), __('License Plate'))
            ->addBreadcrumb(__('Manage Models'), __('Manage Models'));
        return $resultPage;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Model') : __('New Model'),
            $id ? __('Edit Model') : __('New Model')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Models'));
        if ($id) {
            $model = $this->modelRepository->get($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This model no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }

            $this->coreRegistry->register('license_plate_model', $model);

            $resultPage->getConfig()->getTitle()
                ->prepend($model->getId() ? $model->getHmdnr() : __('New Model'));
        }

        return $resultPage;
    }
}