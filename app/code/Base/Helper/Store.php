<?php
namespace Borntechies\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Store
 *
 * @author      Anil <lyudmila@hoofdfabriek.nl>
 */
class Store extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Store constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);

        $this->scopeConfig = $scopeConfig;
    }

    /**
     * get store support email
     *
     * @return string
     */
    public function getStoreSupportEmail(){
        return $this->scopeConfig->getValue('trans_email/ident_custom1/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}