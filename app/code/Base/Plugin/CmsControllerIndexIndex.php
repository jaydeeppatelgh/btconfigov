<?php
namespace Borntechies\Base\Plugin;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Cms\Controller\Index\Index;
use Magento\Framework\Controller\Result\Forward;

/**
 * Class CmsControllerIndexIndex
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class CmsControllerIndexIndex
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param CustomerSession $customerSession
     * @param ForwardFactory $forwardFactory
     */
    public function __construct(
        CustomerSession $customerSession,
        ForwardFactory $forwardFactory
    ) {
        $this->customerSession = $customerSession;
        $this->resultForwardFactory = $forwardFactory;
    }

    /**
     * Redirect customer to account page if customer is
     * logged in
     *
     * @param Index $action
     * @param callable $proceed
     * @param null $coreRoute
     *
     * @return Forward
     */
    public function aroundExecute(
        Index $action,
        callable $proceed,
        $coreRoute = null
    ){
        if($this->customerSession->isLoggedIn()) {
            /** @var Forward $resultForward */
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->setController('account')
                ->setModule('customer');
            $resultForward->forward('index');

            return $resultForward;
        }

        return $proceed();
    }
}