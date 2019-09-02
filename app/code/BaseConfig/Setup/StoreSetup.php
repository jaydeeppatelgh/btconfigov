<?php

namespace Borntechies\BaseConfig\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\WebsiteFactory;
use Magento\Store\Api\WebsiteRepositoryInterface as WebsiteRepository;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;

/**
 * Class StoreSetup
 *
 * @author  Hans van der Molen <hans@hoofdfabriek.nl>
 */
class StoreSetup
{
    /**#@+
     * Constant definitions
     */
    const STORE_CODE_TEMPLATE = '%s_%s';
    /**#@-*/

    /**
     * Resource config
     *
     * @var ResourceConfig
     */
    private $resourceConfig;

    /**
     * Category factory
     *
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * Store factory
     *
     * @var StoreFactory
     */
    private $storeFactory;

    /**
     * Group factory
     *
     * @var GroupFactory
     */
    private $groupFactory;

    /**
     * Website factory
     *
     * @var WebsiteFactory
     */
    private $websiteFactory;

    /**
     * Website repository
     *
     * @var WebsiteRepository
     */
    private $websiteRepository;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var ModuleContextInterface
     */
    protected $context;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    private $eventManager;


    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @param CategoryFactory $categoryFactory
     * @param StoreFactory $storeFactory
     * @param GroupFactory $groupFactory
     * @param WebsiteFactory $websiteFactory
     * @param WebsiteRepository $websiteRepository
     * @param ResourceConfig $resourceConfig
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context,
        CategoryFactory $categoryFactory,
        StoreFactory $storeFactory,
        GroupFactory $groupFactory,
        WebsiteFactory $websiteFactory,
        WebsiteRepository $websiteRepository,
        ResourceConfig $resourceConfig,
        \Magento\Framework\Event\Manager $eventManager
    ) {
        $this->setup = $setup;
        $this->context = $context;
        $this->categoryFactory = $categoryFactory;
        $this->storeFactory = $storeFactory;
        $this->groupFactory = $groupFactory;
        $this->websiteFactory = $websiteFactory;
        $this->websiteRepository = $websiteRepository;
        $this->resourceConfig = $resourceConfig;
        $this->eventManager = $eventManager;
    }

    /**
     * Create websites, stores, and storeviews
     *
     * @return void
     */
    public function createWebsitesStoreStoreviews()
    {
        /**
         * Get default website, store, storeview and category
         */
        $baseWebsite = $this->websiteRepository->getDefault();

        $defaultStoreGroup = $this->groupFactory->create()->load($baseWebsite->getDefaultGroupId());

        $defaultRootCategory = $this->categoryFactory->create()->load($defaultStoreGroup->getRootCategoryId());

        $websites = [];

        if (version_compare($this->context->getVersion(), '2.0.0') < 0) {

            $websites = array(
                'borntechies_de' => array(
                    'name' => 'Borntechies DE',
                    'is_default' => 0,
                    'storeviews' => array(
                        'de' => array(
                            'locale' => 'de_DE',
                            'name' => 'Borntechies German',
                            'is_default' => false,
                        )
                    )
                ),
                'borntechies_es' => array(
                    'name' => 'Borntechies SP',
                    'is_default' => 0,
                    'storeviews' => array(
                        'es' => array(
                            'locale' => 'es_AR',
                            'name' => 'Borntechies Spanish',
                            'is_default' => false,
                        )
                    )
                ),
                'borntechies_en' => array(
                    'name' => 'Borntechies EN',
                    'is_default' => 0,
                    'storeviews' => array(
                        'en' => array(
                            'locale' => 'en_US',
                            'name' => 'Borntechies English',
                            'is_default' => false,
                        )
                    )
                )
            );
        }

        if (version_compare($this->context->getVersion(), '2.0.1') < 0) {

            $websites = array(
                'borntechies_main' => array(
                    'name' => 'Borntechies Main',
                    'is_default' => 0,
                    'storeviews' => array(
                        'nl' => array(
                            'locale' => 'nl_NL',
                            'name' => 'Borntechies Main',
                            'is_default' => false,
                        )
                    )
                )
            );
        }

        if (count($websites) == 0) {
            return;
        }

        /**
         * Create websites, store, and storeviews
         */
        foreach ($websites as $websiteCode => $websiteData) {

            $website = $this->websiteFactory->create()
                ->setData('code', $websiteCode)
                ->setData('name', $websiteData['name'])
                ->setData('sort_order', 0)
                ->setData('is_default', $websiteData['is_default'] ? $websiteData['is_default'] : 0)
                ->save();

            $group = $this->groupFactory->create()
                ->setData('code', $websiteData['name'])
                ->setData('website_id', $website->getId())
                ->setData('name', $websiteData['name'])
                ->setData('root_category_id', $defaultRootCategory->getId())
                ->save();

            $website->setData('default_group_id', $group->getId());

            foreach ($websiteData['storeviews'] as $storeCode => $storeData) {
                $store = $this->storeFactory->create()
                    ->setData('code', sprintf($this::STORE_CODE_TEMPLATE, $websiteCode, $storeCode))
                    ->setData('website_id', $website->getId())
                    ->setData('group_id', $group->getId())
                    ->setData('name', $storeData['name'])
                    ->setData('sort_order', 0)
                    ->setData('is_active', 1)
                    ->save();

                if ($storeData['is_default']) {
                    $group->setData('default_store_id', $store->getId())->save();
                }

                $this->eventManager->dispatch('store_add', ['store' => $store]);

                $this->resourceConfig->saveConfig(
                    'general/locale/code',
                    $storeData['locale'],
                    'stores',
                    $store->getId()
                );

                if (version_compare($this->context->getVersion(), '2.0.1') < 0) {
                    $this->resourceConfig->saveConfig(
                        'web/website_restriction/enabled',
                        0,
                        'websites',
                        $store->getWebsiteId()
                    );

                    $this->resourceConfig->saveConfig(
                        'web/website_restriction/enabled',
                        0,
                        'websites',
                        $store->getWebsiteId()
                    );

                    $this->resourceConfig->saveConfig(
                        'trans_email/ident_general/email',
                        'info@borntechies.nl',
                        'websites',
                        $store->getWebsiteId()
                    );
                }
            }
        }
    }
}
