<?php
namespace Borntechies\LicensePlate\Controller\Result;

use Magento\Customer\Model\Session;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Borntechies\LicensePlate\Helper\Config as ConfigHelper;

/**
 * Class Remove
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Remove extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @param Context $context
     * @param Session $session
     * @param ConfigHelper $config
     */
    public function __construct(
        Context $context,
        Session $session,
        ConfigHelper $config
    ){
        parent::__construct($context);

        $this->customerSession = $session;
        $this->configHelper = $config;
    }

    /**
     * Remove model filter action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $this->customerSession->unsCurrentLicensePlateModel();
        $this->customerSession->unsCurrentLicensePlateQuery();

        if ($this->configHelper->getRedirectBack() && strpos($this->_redirect->getRefererUrl(), $this->getRequest()->getModuleName()) === false)
        {
            $redirect = $this->_redirect->getRefererUrl();
        } else {
            $redirect = '/'.$this->configHelper->getRedirectPage();
        }

        $resultRedirect->setUrl($redirect);

        return $resultRedirect;
    }
}