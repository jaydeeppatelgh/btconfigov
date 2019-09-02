<?php
namespace Borntechies\BaseConfig\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Setup\Exception as SetupException;

/**
 * Class UpgradeData
 *
 * @package Borntechies\BaseConfig\Setup
 *
 * @author  Hans van der Molen <hans@hoofdfabriek.nl>
 */
class UpgradeData implements UpgradeDataInterface
{

    /** @var StoreSetupFactory */
    protected $storeSetupFactory;

    /**
     * @var ContentSetupFactory
     */
    protected $contentSetupFactory;

    /**
     * @var ModuleContextInterface
     */
    protected $context;

    /** @var ThemeSetupFactory */
    protected $themeSetupFactory;

    /**
     * UpgradeData constructor.
     * @param StoreSetupFactory $storeSetupFactory
     * @param ContentSetupFactory $contentSetupFactory
     */
    public function __construct(
        StoreSetupFactory $storeSetupFactory,
        ContentSetupFactory $contentSetupFactory,
        ThemeSetupFactory $themeSetupFactory
    ) {
        $this->storeSetupFactory = $storeSetupFactory;
        $this->contentSetupFactory = $contentSetupFactory;
        $this->themeSetupFactory = $themeSetupFactory;
    }

    /**
     * Upgrade.
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var $storeSetup StoreSetup */
        $storeSetup = $this->storeSetupFactory->create(['setup' => $setup, 'context' => $context]);
        $storeSetup->createWebsitesStoreStoreviews();

        try {
            /** @var $themeSetup ThemeSetup */
            $themeSetup = $this->themeSetupFactory->create(['setup' => $setup, 'context' => $context]);
            $themeSetup->setFrontendTheme();
        } catch (SetupException $e) {
            echo $e->getMessage();
        }


        /**@var $contentSetup ContentSetup */
        $contentSetup = $this->contentSetupFactory->create(['setup' => $setup, 'context' => $context]);
        $contentSetup->installContent();

        $setup->endSetup();
    }
}
