<?php
namespace Borntechies\Import\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Data extends AbstractHelper
{
    const TYPE_CUSTOMER = 'customer';
    const TYPE_PRODUCT  = 'product';
    const TYPE_PRICE    = 'price';
    const TYPE_UPSELL   = 'upsell';
    const TYPE_CATEGORY = 'category';
    const TYPE_LICENSE_PLATE = 'license_plate';
    const TYPE_STOCK    = 'stock';

    const STATUS_RUNNING    = 'running';
    const STATUS_SCHEDULED  = 'scheduled';
    const STATUS_ERROR      = 'error';
    const STATUS_SUCCESS   = 'success';

    const PATH_DELETE_PREVIOUS_PRODUCTS = 'queue_manager/product_import/delete_previous';
    const PATH_PRODUCT_EXPORT_PROFILE   = 'queue_manager/product_import/export_profile';
    const PATH_PRODUCT_IMPORT_PROFILE   = 'queue_manager/product_import/import_profile';
    const PATH_PRODUCT_DELETE_PROFILE   = 'queue_manager/product_import/delete_profile';
    const PATH_PRODUCT_CATEGORY         = 'queue_manager/product_import/category_profile';
    const PATH_PRODUCT_PRICE            = 'queue_manager/product_import/price_profile';
    const PATH_PRODUCT_UPSELL           = 'queue_manager/product_import/upsell_profile';
    const PATH_PRODUCT_STOCK           = 'queue_manager/product_import/stock_profile';

    const PATH_SEND_EMAIL_ON_FAILURE    = 'queue_manager/general/send_email_on_failure';
    const PATH_EMAIL_TO                 = 'queue_manager/general/email_to';
    const PATH_EMAIL_TEMPLATE           = 'queue_manager/general/error_email_template';
    const PATH_EMAIL_IDENTITY           = 'queue_manager/general/error_email_identity';

    const PATH_CUSTOMERS_DIRECTORY      = 'queue_manager/customer_import/file_path';
    const PATH_CUSTOMERS_FILENAME       = 'queue_manager/customer_import/file_name';

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @param Context $context
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     */
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation
    ) {
        parent::__construct($context);
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
    }

    /**
     * @return array
     */
    static public function getAvailableTypes()
    {
        return [
            self::TYPE_CUSTOMER,
            self::TYPE_PRODUCT,
            self::TYPE_PRICE,
            self::TYPE_UPSELL,
            self::TYPE_CATEGORY,
            self::TYPE_STOCK
        ];
    }

    /**
     * Check if delete products that are not in import file is switched on
     *
     * @param string $storeCode
     * @return bool
     */
    public function deletePreviousProducts($storeCode)
    {
        return $this->scopeConfig->isSetFlag(self::PATH_DELETE_PREVIOUS_PRODUCTS, ScopeInterface::SCOPE_STORE, $storeCode);
    }

    /**
     * Get product export profile id from the configuration
     *
     * @return mixed
     */
    public function getProductExportProfile()
    {
        return $this->scopeConfig->getValue(self::PATH_PRODUCT_EXPORT_PROFILE);
    }

    /**
     * Get product import profile id from the configuration
     *
     * @param string $storeCode
     * @return mixed
     */
    public function getProductImportProfile($storeCode)
    {
        return $this->scopeConfig->getValue(self::PATH_PRODUCT_IMPORT_PROFILE, ScopeInterface::SCOPE_STORE, $storeCode);
    }

    /**
     * Get product delete profile id from the configuration
     *
     * @return mixed
     */
    public function getProductDeleteProfile()
    {
        return $this->scopeConfig->getValue(self::PATH_PRODUCT_DELETE_PROFILE);
    }

    /**
     * Get customers import file location
     *
     * @return mixed
     */
    public function getCustomerImportDirectory()
    {
      return $this->scopeConfig->getValue(self::PATH_CUSTOMERS_DIRECTORY);
    }

    /**
     * Get customers file name
     *
     * @return mixed
     */
    public function getCustomerImportFilename()
    {
        return $this->scopeConfig->getValue(self::PATH_CUSTOMERS_FILENAME);
    }

    /**
     * Get price import profile id from the configuration
     *
     * @return mixed
     */
    public function getPriceImportProfile()
    {
        return $this->scopeConfig->getValue(self::PATH_PRODUCT_PRICE);
    }

    /**
     * Get upsell product import profile id from the configuration
     *
     * @return mixed
     */
    public function getUpsellImportProfile()
    {
        return $this->scopeConfig->getValue(self::PATH_PRODUCT_UPSELL);
    }

    /**
     * Get category import profile id
     *
     * @param string $storeCode
     * @return mixed
     */
    public function getCategoryImportProfile($storeCode)
    {
        return $this->scopeConfig->getValue(self::PATH_PRODUCT_CATEGORY, ScopeInterface::SCOPE_STORE, $storeCode);
    }

    /**
     * Get product stock import profile id from the configuration
     *
     * @return mixed
     */
    public function getStockImportProfile()
    {
        return $this->scopeConfig->getValue(self::PATH_PRODUCT_STOCK);
    }

    /**
     * Send email with error report
     *
     * @param string $errors
     *
     * @throws \Magento\Framework\Exception\MailException
     * @return void
     */
    public function sendErrorEmail($errors)
    {
        if ($this->scopeConfig->isSetFlag(self::PATH_SEND_EMAIL_ON_FAILURE) &&
            $this->scopeConfig->getValue(self::PATH_EMAIL_TO)
        ) {
            $this->inlineTranslation->suspend();

            $transport = $this->transportBuilder->setTemplateIdentifier(
                $this->scopeConfig->getValue(
                    self::PATH_EMAIL_TEMPLATE,
                    ScopeInterface::SCOPE_STORE
                )
            )->setTemplateOptions(
                [
                    'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )->setTemplateVars(
                ['errors' => $errors]
            )->setFrom(
                $this->scopeConfig->getValue(
                    self::PATH_EMAIL_IDENTITY,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->addTo(
                $this->scopeConfig->getValue(
                    self::PATH_EMAIL_TO,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->getTransport();

            $transport->sendMessage();

            $this->inlineTranslation->resume();
        }
    }
}
