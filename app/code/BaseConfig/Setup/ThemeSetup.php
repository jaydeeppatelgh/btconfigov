<?php
namespace Borntechies\BaseConfig\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Theme\Model\ResourceModel\Theme as ResourceTheme;
use Magento\Theme\Model\Theme;
use Magento\Setup\Exception as SetupException;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Theme\Model\Data\Design\Config as DesignConfig;
use Magento\Store\Api\StoreRepositoryInterface as StoreRepository;
use Magento\Theme\Model\Theme\Registration;

/**
 * Class ThemeSetup
 *
 * @author  Anil <lyudmila@hoofdfabriek.nl>
 */
class ThemeSetup
{
    /**#@+
     * Constant definitions
     */
    const THEME_CODE = 'Borntechies/mainwebsite';
    /**#@-*/

    /**#@+
     * Constant definitions
     */
    const THEME_ENG_CODE = 'Borntechies/english';
    /**#@-*/

    /**
     * @var ResourceConfig
     */
    protected $resourceConfig;

    /**
     * @var ResourceTheme
     */
    protected $resourceTheme;

    /**
     * @var Theme
     */
    protected $theme;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var ModuleContextInterface
     */
    protected $context;


    /** @var ReinitableConfigInterface */
    protected $reinitableConfig;

    /** @var IndexerRegistry */
    protected $indexerRegistry;

    /**
     * @var StoreRepository
     */
    private $storeRepository;

    /**
     * Theme registration
     *
     * @var Registration
     */
    private $themeRegistration;

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @param ResourceConfig $resourceConfig
     * @param ResourceTheme $resourceTheme
     * @param Theme $theme
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context,
        ResourceConfig $resourceConfig,
        ResourceTheme $resourceTheme,
        Theme $theme,
        ReinitableConfigInterface $reinitableConfig,
        IndexerRegistry $indexerRegistry,
        StoreRepository $storeRepository,
        Registration $themeRegistration
    ) {
        $this->setup = $setup;
        $this->context = $context;
        $this->resourceConfig = $resourceConfig;
        $this->resourceTheme = $resourceTheme;
        $this->theme = $theme;
        $this->reinitableConfig = $reinitableConfig;
        $this->indexerRegistry = $indexerRegistry;
        $this->storeRepository = $storeRepository;
        $this->themeRegistration = $themeRegistration;
    }

    /**
     * Set the theme
     *
     * @throws SetupException
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setFrontendTheme()
    {
        if (version_compare($this->context->getVersion(), '2.0.3') < 0) {
            $store = $this->storeRepository->get('borntechies_main_nl');
            $this->themeRegistration->register();
            $this->reinitableConfig->reinit();
            
            $this->resourceTheme->load($this->theme, $this::THEME_CODE, 'code');
            if (!$this->theme->getThemeId()) {
                throw new SetupException('Could not load theme with code "' . $this::THEME_CODE . '"');
            }
            if ($this->theme->getData('area') != 'frontend') {
                throw new SetupException('Theme "' . $this::THEME_CODE . '" is not a frontend theme');
            }
            if (!$this->theme->isPhysical()) {
                throw new SetupException('Theme "' . $this::THEME_CODE . '" is not a physical theme');
            }

            /**
             * Save theme
             */
            $this->resourceConfig->saveConfig(DesignInterface::XML_PATH_THEME_ID, $this->theme->getThemeId(), 'websites', $store->getWebsiteId());
            $this->resourceConfig->saveConfig('design/footer/copyright', 'Copyright © Borntechies B.V.', 'websites', $store->getWebsiteId());
            $this->reinitableConfig->reinit();
            $this->indexerRegistry->get(DesignConfig::DESIGN_CONFIG_GRID_INDEXER_ID)->reindexAll();
        }

        if (version_compare($this->context->getVersion(), '2.1.4') < 0) {
            $store = $this->storeRepository->get('borntechies_view');
            $this->themeRegistration->register();
            $this->reinitableConfig->reinit();
            
            $this->resourceTheme->load($this->theme, $this::THEME_ENG_CODE, 'code');
            if (!$this->theme->getThemeId()) {
                throw new SetupException('Could not load theme with code "' . $this::THEME_ENG_CODE . '"');
            }
            if ($this->theme->getData('area') != 'frontend') {
                throw new SetupException('Theme "' . $this::THEME_ENG_CODE . '" is not a frontend theme');
            }
            if (!$this->theme->isPhysical()) {
                throw new SetupException('Theme "' . $this::THEME_ENG_CODE . '" is not a physical theme');
            }

            /**
             * Save theme
             */
            $this->resourceConfig->saveConfig(DesignInterface::XML_PATH_THEME_ID, $this->theme->getThemeId(), 'websites', $store->getWebsiteId());
            $this->reinitableConfig->reinit();
            $this->indexerRegistry->get(DesignConfig::DESIGN_CONFIG_GRID_INDEXER_ID)->reindexAll();
        }
    }
}
