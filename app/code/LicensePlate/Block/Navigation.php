<?php
namespace Borntechies\LicensePlate\Block;

use Magento\Customer\Model\Session;
use Magento\Catalog\Model\Layer\AvailabilityFlagInterface;
use Magento\Catalog\Model\Layer\FilterList;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Navigation
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Navigation extends \Magento\LayeredNavigation\Block\Navigation
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param Resolver $layerResolver
     * @param FilterList $filterList
     * @param AvailabilityFlagInterface $visibilityFlag
     * @param Session $session
     * @param array $data
     */
    public function __construct(
        Context $context,
        Resolver $layerResolver,
        FilterList $filterList,
        AvailabilityFlagInterface $visibilityFlag,
        Session $session,
        array $data = []
    ) {
        parent::__construct($context, $layerResolver, $filterList, $visibilityFlag, $data);

        $this->customerSession = $session;
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        if (!$this->customerSession->getCurrentLicensePlateModel()) {
            return false;
        }
        return parent::canShowBlock();
    }

    /**
     * Apply layer
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if (!$this->customerSession->getCurrentLicensePlateModel()) {
            return $this;
        }

        return parent::_prepareLayout();
    }
}