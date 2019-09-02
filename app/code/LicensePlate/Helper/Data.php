<?php
namespace Borntechies\LicensePlate\Helper;

use Magento\Framework\App\Helper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Customer\Model\Session;
use Borntechies\LicensePlate\Api\Data\ModelInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Data extends Helper\AbstractHelper
{
    /**
     * Magento string lib
     *
     * @var String
     */
    protected $string;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * Construct
     *
     * @param Context $context
     * @param StringUtils $string
     * @param Escaper $escaper
     */
    public function __construct(
        Context $context,
        StringUtils $string,
        Escaper $escaper,
        Session $session
    ) {
        parent::__construct($context);

        $this->string = $string;
        $this->escaper = $escaper;
        $this->customerSession = $session;
    }

    /**
     * Get search result url
     *
     * @param null $query
     *
     * @return string
     */
    public function getResultUrl($query = null)
    {
        return $this->_getUrl(
            'licenseplatesearch/result',
            ['_query' => [Config::QUERY_VAR_NAME => $query], '_secure' => $this->_request->isSecure()]
        );
    }

    /**
     * Get search query parameter
     *
     * @return string
     */
    public function getQueryParamName()
    {
        return Config::QUERY_VAR_NAME;
    }

    /**
     * Retrieve HTML escaped search query
     *
     * @return string
     */
    public function getEscapedQueryText()
    {
        return $this->escaper->escapeHtml(
            $this->getPreparedQueryText($this->getQueryText(), $this->getMaxQueryLength())
        );
    }

    /**
     * Retrieve maximum query length
     *
     * @return int
     */
    public function getQueryLength()
    {
        return Config::QUERY_LENGTH;
    }

    /**
     * Retrieve maximum query length
     *
     * @return int
     */
    public function getMaxQueryLength()
    {
        return Config::MAX_QUERY_LENGTH;
    }

    /**
     * Retrieve search query text
     *
     * @param bool $removeDashes
     *
     * @return string
     */
    public function getQueryText($removeDashes = false)
    {
        $queryText = $this->_request->getParam($this->getQueryParamName());

        if (!$queryText && $this->customerSession->getCurrentLicensePlateQuery()) {
            $queryText = $this->customerSession->getCurrentLicensePlateQuery();
        }

        if ($removeDashes) {
            $queryText = str_replace('-','',$queryText);
        }

        return($queryText === null || is_array($queryText))
            ? ''
            : $this->string->cleanString(trim($queryText));
    }

    /**
     * Search query text
     *
     * @param string $queryText
     * @param int|string $maxQueryLength
     *
     * @return string
     */
    private function getPreparedQueryText($queryText, $maxQueryLength)
    {
        if ($this->isQueryTooLong($queryText, $maxQueryLength)) {
            $queryText = $this->string->substr($queryText, 0, $maxQueryLength);
        }
        return $queryText;
    }

    /**
     * Check if query is not over max allowed length
     *
     * @param string $queryText
     * @param int|string $maxQueryLength
     *
     * @return bool
     */
    private function isQueryTooLong($queryText, $maxQueryLength)
    {
        return ($maxQueryLength !== '' && $this->string->strlen($queryText) > $maxQueryLength);
    }

    /**
     * Check if query length is correct
     *
     * @return bool
     */
    public function isMinQueryLength()
    {
        return $this->string->strlen($this->getQueryText()) < Config::QUERY_LENGTH;
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

    /**
     * Can show form on frontend
     *
     * @return bool
     */
    public function canShowForm()
    {
        return $this->scopeConfig->isSetFlag('license_plate_settings/general/show_form', ScopeInterface::SCOPE_STORE);
    }
}