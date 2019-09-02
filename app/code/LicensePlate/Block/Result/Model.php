<?php
namespace Borntechies\LicensePlate\Block\Result;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Borntechies\LicensePlate\Api\Data\ModelInterface;

/**
 * Class Model
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Model extends Template
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param Template\Context $context
     * @param Session $session
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Session $session,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->customerSession = $session;
    }

    /**
     * Get Model information from registry
     *
     * @return null|ModelInterface
     */
    public function getModelInfo()
    {
        return $this->customerSession->getCurrentLicensePlateModel();
    }
}