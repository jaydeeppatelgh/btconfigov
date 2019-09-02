<?php
namespace Borntechies\Base\Setup;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\Data\BlockInterfaceFactory;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var BlockInterfaceFactory
     */
    private $blockFactory;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @param PageFactory $pageFactory
     * @param BlockRepositoryInterface $blockRepository
     * @param BlockInterfaceFactory $blockFactory
     */
    public function __construct(
        PageFactory $pageFactory,
        BlockRepositoryInterface $blockRepository,
        BlockInterfaceFactory $blockFactory
    )
    {
        $this->pageFactory = $pageFactory;
        $this->blockRepository = $blockRepository;
        $this->blockFactory = $blockFactory;
    }


    /**
     * Upgrade data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $cmsBlock = $this->blockFactory->create();

            $footer_service_link = $cmsBlock->load('footer_service_link', 'identifier');
            if (!$footer_service_link->getId()) {
                $this->createFooterLinkBlock();
            }
        }

        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $setup->startSetup();
            $connection = $setup->getConnection();

            //Order address table
            $connection->addColumn(
                $setup->getTable('sales_order_item'),
                'dealer_nr',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' =>'Dealer NR'

                ]
            );
            $setup->endSetup();
        }
    }

    public function createFooterLinkBlock()
    {
        $cmsBlock = $this->blockFactory->create();

        $content = <<<EOD
<ul class="footer-links logo-footer">
<li><img src="{{view url="images/footer-logo.svg"}}" alt="Borntechies" /></li>
</ul>
<ul class="footer-links">
<li>
<h3 class="footer-title">Service</h3>
</li>
<li><a href="{{store url="downloads"}}">Downloads</a></li>
<li><a title="Verkoopvoorwaarden" href="{{store url="verkoopvoorwaarden"}}">Verkoopvoorwaarden</a></li>
<li><a title="Actiefolder" href="{{store url="actiefolder"}}">Actiefolder</a></li>
<li><a title="Garantie en retour" href="{{store url="garantie-en-retour"}}">Garantie&nbsp;en retouren</a></li>
</ul><ul class="footer-links">
<li>
<h3 class="footer-title">Quicklinks</h3>
</li>
<li><a href="{{store url="catalogsearch/advanced"}}">Geavanceerd zoeken</a></li>
<li><a href="{{store url="privacy-policy-cookie-policy"}}">Privacy and Cookie Policy</a></li>
<li><a href="{{store url="contact"}}">Contact</a></li>
</ul><ul class="footer-links">
<li>
<h3 class="footer-title">Mijn Borntechies</h3>
</li>
<li><a href="{{store url="customer/account/login/"}}">Login</a></li>
<li><a href="{{store url="customer/account/"}}">Mijn bestellingen</a></li>
<li><a href="{{store url="/"}}">Shop</a></li>
</ul><ul class="footer-links">
<li>
<h3 class="footer-title">Kunnen we u helpen?</h3>
</li>
<li class="info">Tt. Vasumweg 112</li>
<li class="info">1033 SH Amsterdam</li>
<li class="info">020 - 68 80 348</li>
<li><a href="mailto:info@borntechies.n">info@borntechies.nl</a></li>
</ul>
EOD;
        $identifier = "footer_service_link";
        $title = "Footer Service Links ";

        // \Magento\Cms\Api\Data\BlockInterface to set data to our CMS block
        $cmsBlock->setData('stores', [0])
            ->setIdentifier($identifier)
            ->setIsActive(1)
            ->setTitle($title)
            ->setContent($content);
        $this->blockRepository->save($cmsBlock);
    }
}
