<?php
namespace Borntechies\LicensePlate\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Borntechies\LicensePlate\Helper\Data as DataHelper;
use Magento\Customer\Model\Session;
use Borntechies\LicensePlate\Model\ResourceModel\LicensePlate\Collection;
use Borntechies\LicensePlate\Helper\Config;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Catalog\Block\Product\ListProduct;
use Amasty\Shopby\Helper\Category as CategoryHelper;
use Magento\Search\Model\SearchEngine;
use Magento\Catalog\Api\CategoryRepositoryInterface;

/**
 * Class Result
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Result extends Template
{
    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $catalogLayer;

    /**
     * @var DataHelper
     */
    protected $helperData;

    /**
     * Catalog Product collection
     *
     * @var Collection
     */
    protected $productCollection;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var SearchEngine
     */
    private $searchEngine;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Catalog\Model\Category|null
     */
    protected $currentCategoryFilter;

    /**
     * @var null|\Magento\Catalog\Model\Category[]
     */
    protected $categoryList = null;

    /**
     * @var bool
     */
    protected $canShowProduct = true;

    /**
     * @param DataHelper $dataHelper
     * @param LayerResolver $layerResolver
     * @param Template\Context $context
     * @param Session $session
     * @param array $data
     */
    public function __construct(
        DataHelper $dataHelper,
        LayerResolver $layerResolver,
        Template\Context $context,
        Session $session,
        SearchEngine $searchEngine,
        CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->catalogLayer = $layerResolver->get();
        $this->helperData = $dataHelper;
        $this->customerSession = $session;
        $this->searchEngine = $searchEngine;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Prepare layout
     *
     * @return $this
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\StateException
     */
    protected function _prepareLayout()
    {
        $title = $this->getSearchQueryText();
        $this->pageConfig->getTitle()->set($title);
        // add Home breadcrumb
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            )->addCrumb(
                'licenseplatesearch',
                ['label' => $title, 'title' => $title]
            );
        }
        if (!$this->getRequest()->getParam('shopbyAjax')) {
            $categoryFilterable = $this->getCategoryList();

            if (!empty($categoryFilterable) && count($categoryFilterable) > 1) {
                $this->getLayout()->unsetElement('catalogsearch.leftnav');
                $this->canShowProduct = false;
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Get search query text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getSearchQueryText()
    {
        if ($this->helperData->getEscapedQueryText()) {
            return __("Search results for: '%1' license plate", $this->helperData->getEscapedQueryText());
        }
        /** @var \Borntechies\LicensePlate\Api\Data\ModelInterface $model*/
        if ($model = $this->customerSession->getCurrentLicensePlateModel()) {
            return __("Search results for: '%1 %2'", $model->getMake(), $model->getModel());
        }

        return __('Search results');
    }

    /**
     * Set order options
     *
     * @return void
     */
    public function setListOrders()
    {
        /* @var $category \Magento\Catalog\Model\Category */
        $category = $this->catalogLayer->getCurrentCategory();

        $availableOrders = $category->getAvailableSortByOptions();
        unset($availableOrders['position']);

        $this->getChildBlock('search_result_list')->setAvailableOrders($availableOrders);
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Collection
     */
    protected function _getProductCollection()
    {
        if (null === $this->productCollection) {
            $this->productCollection = $this->getListBlock()->getLoadedProductCollection();
        }

        return $this->productCollection;
    }

    /**
     * Retrieve search list toolbar block
     *
     * @return ListProduct
     */
    public function getListBlock()
    {
        return $this->getChildBlock('search_result_list');
    }

    /**
     * Retrieve search result count
     *
     * @return string
     */
    public function getResultCount()
    {
        if (!$this->customerSession->getCurrentLicensePlateModel()) {
            $this->setResultCount(0);
        }

        if ($this->getData('result_count') === null) {
            $size = $this->_getProductCollection()->getSize();
            $this->setResultCount($size);
        }
        return $this->getData('result_count');
    }

    /**
     * @return string
     */
    public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    /**
     * Retrieve No Result or Minimum query length Text
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getNoResultText()
    {
        if ($this->helperData->getEscapedQueryText() && $this->helperData->isMinQueryLength()) {
            return __('Minimum Search query length is %1', Config::QUERY_LENGTH);
        }

        return $this->_getData('no_result_text');
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     * @throws LocalizedException
     */
    public function getAdditionalHtml()
    {
        return $this->getLayout()->getBlock('search_result_list')->getChildHtml('additional');
    }

    /**
     * Get category list applicable to current search
     *
     * @return array
     * @throws \Magento\Framework\Exception\StateException
     */
    public function getCategoryList()
    {
        if ($this->categoryList === null) {
            /** @var \Amasty\Shopby\Model\ResourceModel\Fulltext\Collection $productCollection */
            $productCollection = $this->catalogLayer->getProductCollection();
            $alteredQueryResponse = $this->getAlteredQueryResponse();
            $optionsFacetedData = $productCollection->getFacetedData('category', $alteredQueryResponse);

            $currentCategory = $this->getCurrentCategoryFiltered();
            $result = [];
            foreach ($currentCategory->getChildrenCategories() as $child) {
                if ($child->getIsActive() && isset($optionsFacetedData[$child->getId()])) {
                    $result[] = $child;
                }
            }

            $this->categoryList = $result;
        }

        return $this->categoryList;
    }

    /**
     * Get applied category filter
     *
     * @return \Magento\Catalog\Api\Data\CategoryInterface|\Magento\Catalog\Model\Category
     */
    protected function getCurrentCategoryFiltered()
    {
        if (!$this->currentCategoryFilter) {
            if ($id = $this->_request->getParam('cat')) {
                try {
                    $this->currentCategoryFilter = $this->categoryRepository->get($id, $this->_storeManager->getStore()->getId());
                } catch (LocalizedException $e) {
                    $this->currentCategoryFilter = $this->catalogLayer->getCurrentCategory();
                }
            } else {
                $this->currentCategoryFilter = $this->catalogLayer->getCurrentCategory();
            }

        }

        return $this->currentCategoryFilter;
    }

    /**
     * @return \Magento\Catalog\Model\Category[]|\Magento\Framework\DataObject[]
     */
    public function getAppliedCategoriesList()
    {
        $category = $this->getCurrentCategoryFiltered();

        return $category->getParentCategories();
    }

    /**
     * @return \Magento\Framework\Search\ResponseInterface|null
     */
    private function getAlteredQueryResponse()
    {
        $alteredQueryResponse = null;

        /** @var \Amasty\Shopby\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->catalogLayer->getProductCollection();
        $requestBuilder = clone $productCollection->getMemRequestBuilder();
        $requestBuilder->removePlaceholder(CategoryHelper::ATTRIBUTE_CODE);

        $startCategoryForCountBucket = $this->getCurrentCategoryFiltered()->getId();
        $requestBuilder->bind(CategoryHelper::ATTRIBUTE_CODE, $startCategoryForCountBucket);

        $queryRequest = $requestBuilder->create();

        $alteredQueryResponse = $this->searchEngine->search($queryRequest);



        return $alteredQueryResponse;
    }

    /**
     * @return string
     */
    public function buildCategoryUrl($optionValue = null)
    {
        return $this->_urlBuilder->getUrl('*/*/*', [
            '_use_rewrite' => true,
            'cat' => $optionValue,
            'model_id' => $this->_request->getParam('model_id'),
            'r' => $this->_request->getParam('r')
        ]);
    }

    /**
     * @return bool
     */
    public function canShowProduct()
    {
        return $this->canShowProduct;
    }
}