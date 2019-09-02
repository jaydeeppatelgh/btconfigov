<?php
namespace Borntechies\Catalog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Data extends AbstractHelper
{
    /**
     * @var StockRegistryInterface
     */
    protected $stockRepository;

    /**
     * @var ConfigInterface
     */
    protected $productTypeConfig;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Context $context
     * @param StockRegistryInterface $stockRepository
     * @param ConfigInterface $configInterface
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        StockRegistryInterface $stockRepository,
        ConfigInterface $configInterface,
        ScopeConfigInterface $scopeConfig
    ){
        parent::__construct($context);

        $this->stockRepository = $stockRepository;
        $this->productTypeConfig = $configInterface;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get Validation Rules for Quantity field
     *
     *  @param int $minQty
     *
     * @return array
     */
    public function getQuantityValidators($minQty)
    {
        $validators = [];
        $validators['required-number'] = true;
        $validators['validate-item-quantity'] = ['minAllowed' => $minQty];
        return $validators;
    }

    /**
     * Get product default qty value
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return int|null
     */
    public function getProductDefaultQty($product)
    {
        $qty = $this->getMinimalQty($product);
        $config = $product->getPreconfiguredValues();
        $configQty = $config->getQty();
        if ($configQty > $qty) {
            $qty = $configQty;
        }

        return $qty;
    }

    /**
     * Gets minimal sales quantity
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return int|null
     */
    public function getMinimalQty($product)
    {
        $stockItem = $this->stockRepository->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        $minSaleQty = $stockItem->getMinSaleQty();
        return $minSaleQty > 0 ? $minSaleQty : null;
    }

    /**
     * Check whether quantity field should be rendered
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    public function shouldRenderQuantity($product)
    {
        return !$this->productTypeConfig->isProductSet($product->getTypeId());
    }

    /**
     * Check whether redirect to cart enabled
     *
     * @return bool
     */
    public function isRedirectToCartEnabled()
    {
        return $this->scopeConfig->getValue(
            'checkout/cart/redirect_to_cart',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get css style class for product custom stock status label
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getStatusDecoration($product)
    {
        switch (strtolower($product->getAttributeText('stock_text'))) {
            case 'op voorraad':
            case 'auf lager':
            case 'en stock':
            case 'in stock':
                return 'available';
            case 'momenteel geen voorraad':
            case 'momenteel niet op voorraad':
            case 'zur zeit kein lager':
            case 'bel voor voorraad':
            case 'anruf nach vorrat':
            case 'llamar para stock':
            case 'call for supply':
                return 'available-call';
            case 'leverbaar &lt; 24 uur':
            case 'lieferung &lt; 48 stunden':
            case 'disponible &lt; 48 horas':
            case 'delivery &lt; 48 hours':
                return 'available-24';
        }
    }
}
