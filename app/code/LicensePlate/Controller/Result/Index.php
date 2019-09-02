<?php
namespace Borntechies\LicensePlate\Controller\Result;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\Action\Context;
use Borntechies\LicensePlate\Helper\Data as DataHelper;
use Borntechies\LicensePlate\Api\ModelRegistrationRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Borntechies\LicensePlate\Api\ModelRepositoryInterface;

/**
 * Class Index
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Index extends \Magento\Framework\App\Action\Action
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
     * @var ModelRegistrationRepositoryInterface
     */
    protected $modelRegistrationRepository;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param Resolver $layerResolver
     * @param ModelRegistrationRepositoryInterface $registrationRepository
     * @param ModelRepositoryInterface $modelRepository
     * @param PageFactory $resultPageFactory
     * @param Session $session
     * @param DataHelper $data
     */
    public function __construct(
        Context $context,
        Resolver $layerResolver,
        ModelRegistrationRepositoryInterface $registrationRepository,
        ModelRepositoryInterface $modelRepository,
        PageFactory $resultPageFactory,
        Session $session,
        DataHelper $data
    ) {
        parent::__construct($context);
        $this->layerResolver = $layerResolver;
        $this->resultPageFactory = $resultPageFactory;
        $this->modelRepository = $modelRepository;
        $this->modelRegistrationRepository = $registrationRepository;
        $this->customerSession = $session;
        $this->dataHelper = $data;
    }

    /**
     * Display search result
     *
     * @return \Magento\Framework\View\Result\Page|\Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $this->layerResolver->create(Resolver::CATALOG_LAYER_CATEGORY);

        $query = $this->dataHelper->getQueryText(true);

        if ($query != '') {
            try {
                $this->customerSession->unsCurrentLicensePlateModel();
                $this->customerSession->unsCurrentLicensePlateQuery();
                $registration = $this->modelRegistrationRepository->getByRegistration($query);
                $model = $this->modelRepository->get($registration->getModelId());

                $this->customerSession->setCurrentLicensePlateModel($model);
                $this->customerSession->setCurrentLicensePlateQuery($this->dataHelper->getQueryText());
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(__('Registration with number "%1" does not exist.', $this->dataHelper->getQueryText()));
            }

            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            return $resultPage;
        } else {
            return $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
        }
    }
}