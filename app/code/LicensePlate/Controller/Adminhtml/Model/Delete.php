<?php
namespace Borntechies\LicensePlate\Controller\Adminhtml\Model;

use Magento\Backend\App\Action;
use Borntechies\LicensePlate\Api\ModelRepositoryInterface;

/**
 * Class Delete
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Delete extends Action
{
    /**
     * @var ModelRepositoryInterface
     */
    protected $modelRepository;

    /**
     * Delete constructor.
     *
     * @param Action\Context           $context
     * @param ModelRepositoryInterface $modelRepository
     */
    public function __construct(
        Action\Context $context,
        ModelRepositoryInterface $modelRepository
    )
    {
        parent::__construct($context);
        $this->modelRepository = $modelRepository;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->modelRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the model.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a model to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}