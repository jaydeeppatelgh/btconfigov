<?php
namespace Borntechies\LicensePlate\Controller\Result;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Borntechies\LicensePlate\Api\ModelRepositoryInterface;

/**
 * Class Extended
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Extended extends \Magento\Framework\App\Action\Action
{
    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    protected $layerResolver;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var ModelRepositoryInterface
     */
    protected $modelRepository;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * Extended constructor.
     * @param Context $context
     * @param Resolver $layerResolver
     * @param ModelRepositoryInterface $modelRepository
     * @param PageFactory $resultPageFactory
     * @param Session $session
     */
    public function __construct(
        Context $context,
        Resolver $layerResolver,
        ModelRepositoryInterface $modelRepository,
        PageFactory $resultPageFactory,
        Session $session
    ) {
        parent::__construct($context);

        $this->layerResolver = $layerResolver;
        $this->resultPageFactory = $resultPageFactory;
        $this->modelRepository = $modelRepository;
        $this->customerSession = $session;
    }

    /**
     * Display search result
     *
     * @return \Magento\Framework\View\Result\Page|\Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $this->layerResolver->create(Resolver::CATALOG_LAYER_CATEGORY);
        $modelId = $this->getRequest()->getParam('model_id');

        if ($modelId) {
            try {
                $this->customerSession->unsCurrentLicensePlateModel();
                $model = $this->modelRepository->get($modelId);
                $this->customerSession->setCurrentLicensePlateModel($model);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            return $resultPage;
        } else {
            return $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
        }
    }
}