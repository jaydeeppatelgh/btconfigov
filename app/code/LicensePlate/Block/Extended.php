<?php
namespace Borntechies\LicensePlate\Block;

use Magento\Framework\View\Element\Template;
use Borntechies\LicensePlate\Api\Data\ModelInterfaceFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Extended
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Extended extends Template
{
    /**
     * @var \Borntechies\LicensePlate\Model\Model
     */
    protected $model;

    /**
     * @param Template\Context $context
     * @param ModelInterfaceFactory $model
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ModelInterfaceFactory $model,
        array $data
    ) {
        parent::__construct($context, $data);

        $this->model = $model->create();
    }

    /**
     * Get existing Manufacturers
     *
     * @return string
     */
    public function getManufactures()
    {
        $manufactures = $this->model->getResource()->getManufacturers();
        $preparedOptions = [];
        foreach ($manufactures as $key => $manufacture) {
            $preparedOptions[] = [
                'value' => $manufacture,
                'title' => $manufacture
            ];
        }
        return json_encode($preparedOptions);
    }

    /**
     * Get form update url
     *
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->_urlBuilder->getUrl( 'licenseplatesearch/result/update',
            ['_secure' => $this->_request->isSecure()]);
    }

    /**
     * Get extended search result url
     *
     * @return string
     */
    public function getExtendedResultUrl()
    {
        return $this->_urlBuilder->getUrl(
            'licenseplatesearch/result/extended',
            ['_secure' => $this->_request->isSecure()]
        );
    }

    /**
     * Can show form
     *
     * @return bool
     */
    public function canShowForm()
    {
        return $this->_scopeConfig->isSetFlag('license_plate_settings/general/show_form', ScopeInterface::SCOPE_STORE);
    }
}