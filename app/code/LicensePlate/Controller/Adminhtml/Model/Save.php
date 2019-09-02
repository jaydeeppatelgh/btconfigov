<?php
namespace Borntechies\LicensePlate\Controller\Adminhtml\Model;

use Borntechies\LicensePlate\Api\Data\ModelInterfaceFactory;
use Borntechies\LicensePlate\Api\Data\ModelProductInterfaceFactory;
use Borntechies\LicensePlate\Api\Data\ModelRegistrationInterfaceFactory;
use Borntechies\LicensePlate\Api\ModelProductRepositoryInterface;
use Borntechies\LicensePlate\Api\ModelRegistrationRepositoryInterface;
use Borntechies\LicensePlate\Model\Model;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Borntechies\LicensePlate\Api\ModelRepositoryInterface;
use Borntechies\LicensePlate\Helper\Validation;

/**
 * Class Save
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Save extends Action
{
    /**
     * @var ModelInterfaceFactory
     */
    protected $modelFactory;

    /**
     * @var ModelRepositoryInterface
     */
    protected $modelRepository;

    /**
     * @var ModelRegistrationRepositoryInterface
     */
    protected $registrationRepository;
    /**
     * @var ModelRegistrationInterfaceFactory
     */
    protected $registrationFactory;

    /**
     * @var ModelProductRepositoryInterface
     */
    protected $modelProductRepository;

    /**
     * @var ModelProductInterfaceFactory
     */
    protected $modelProductFactory;

    /**
     * Save constructor.
     *
     * @param Action\Context                       $context
     * @param ModelInterfaceFactory                $modelFactory
     * @param ModelRepositoryInterface             $modelRepository
     * @param ModelRegistrationRepositoryInterface $registrationRepository
     * @param ModelRegistrationInterfaceFactory    $registrationFactory
     * @param ModelProductRepositoryInterface      $modelProductRepository
     * @param ModelProductInterfaceFactory         $modelProductFactory
     */
    public function __construct(
        Action\Context $context,
        ModelInterfaceFactory $modelFactory,
        ModelRepositoryInterface $modelRepository,
        ModelRegistrationRepositoryInterface $registrationRepository,
        ModelRegistrationInterfaceFactory $registrationFactory,
        ModelProductRepositoryInterface $modelProductRepository,
        ModelProductInterfaceFactory $modelProductFactory
    ) {
        parent::__construct($context);

        $this->modelFactory = $modelFactory;
        $this->modelRepository = $modelRepository;
        $this->registrationRepository = $registrationRepository;
        $this->registrationFactory = $registrationFactory;
        $this->modelProductRepository = $modelProductRepository;
        $this->modelProductFactory = $modelProductFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $modelData = $this->getRequest()->getPost('licenseplate_model', []);

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($modelData) {
            $id = isset($modelData['current_model_id']) ? $modelData['current_model_id'] : null;
            try {
                /**@var Model $model */
                $model = $this->modelFactory->create();
                if ($id) {
                    $model = $this->modelRepository->get($id);
                    $modelData['id'] = $id;
                }

                if (isset($data['links']['model'])) {
                    $modelData['products']  = $data['links']['model'];
                }

                $registrations = [];
                if (isset($modelData['registrations'])) {
                    $registrations = $modelData['registrations'];
                    unset($modelData['registrations']);
                }

                $products = [];
                if (isset($data['links']['model'])) {
                    $products = $data['links']['model'];
                }

                $validationResult = Validation::validateModel($modelData);
                if ($validationResult !== true) {
                    foreach ($validationResult as $error) {
                        $this->messageManager->addErrorMessage($error);
                    }
                    throw new LocalizedException(__('Model is not valid'));
                }

                $model->setData($modelData);

                $this->_eventManager->dispatch(
                    'adminhtml_licenseplate_model_prepare_save',
                    ['model' => $model, 'request' => $this->getRequest()]
                );

                $this->modelRepository->save($model);

                foreach ($registrations as $registration) {
                    $registrationLink = $this->registrationFactory->create();
                    $registrationId = (isset($registration['id']) && $registration['id']) ? $registration['id'] : null;
                    if ($registrationId) {
                        $registrationLink = $this->registrationRepository->get($registrationId);
                    }
                    if (isset($registration['is_delete']) && $registration['is_delete']) {
                        $this->registrationRepository->delete($registrationLink);
                    } else {
                        $registrationLink->setRegistration($registration['registration']);
                        $registrationLink->setModelId($model->getId());
                        $this->registrationRepository->save($registrationLink);
                    }
                }

                $productIds = [];
                foreach ($products as $product) {
                    $productIds[] = $product['id'];
                }
                foreach ($this->modelProductRepository->getModelProducts($model) as $productLink) {
                    if (!in_array($productLink->getProductId(), $productIds)) {
                        $this->modelProductRepository->delete($productLink);
                    } else {
                        $key = array_search($productLink->getProductId(), $productIds);
                        unset($productIds[$key]);
                    }
                }

                if (count($productIds)) {
                    foreach ($productIds as $productId) {
                        $productLink = $this->modelProductFactory->create();
                        $productLink->setModelId($model->getId());
                        $productLink->setProductId($productId);
                        $this->modelProductRepository->save($productLink);
                    }
                }

                $this->messageManager->addSuccessMessage(__('You saved the model.'));

                $this->_eventManager->dispatch(
                    'adminhtml_licenseplate_model_save_after',
                    ['model' => $model, 'request' => $this->getRequest()]
                );

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit', ['id' => $model->getId(), '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the company.'));
            }

            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}