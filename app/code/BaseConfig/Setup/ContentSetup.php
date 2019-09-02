<?php

namespace Borntechies\BaseConfig\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\BlockRepository;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\PageRepository;
use Magento\Cms\Model\ResourceModel\Block as ResourceBlock;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

/**
 * Class ContentSetup
 *
 * @author  Anil <lyudmila@hoofdfabriek.nl>
 */
class ContentSetup
{

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var BlockRepository
     */
    protected $blockRepository;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var ResourceBlock
     */
    protected $resource;

    /**
     * @var ModuleDataSetupInterface
     */
    protected $setup;

    /**
     * @var ModuleContextInterface
     */
    protected $context;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * ContentSetup constructor.
     * @param ResourceBlock $resource
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @param BlockFactory $blockFactory
     * @param BlockRepository $blockRepository
     * @param PageFactory $pageFactory
     * @param PageRepository $pageRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceBlock $resource,
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context,
        BlockFactory $blockFactory,
        BlockRepository $blockRepository,
        PageFactory $pageFactory,
        PageRepository $pageRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->setup = $setup;
        $this->context = $context;
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
        $this->pageFactory = $pageFactory;
        $this->pageRepository = $pageRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Installs content
     */
    public function installContent()
    {
        $context = $this->context;

        $borntechiesMainStore = $this->storeManager->getStore('borntechies_main_nl');
        $borntechiesViewStore = $this->storeManager->getStore('borntechies_view');
        if (version_compare($this->context->getVersion(), '2.0.7') < 0) {
            $homepage = $this->pageRepository->getById('home');

            $storeIds = [];
            foreach ($this->storeManager->getStores() as $store) {
                $storeIds[] = $store->getId();
            }
            $homepage->setStores(array_diff($storeIds, [$borntechiesMainStore->getId()]));
            $this->pageRepository->save($homepage);

            $footerBlock = $this->blockRepository->getById('footer_service_link');
            $footerBlock->setStores(array_diff($storeIds, [$borntechiesMainStore->getId()]));
            $this->blockRepository->save($footerBlock);
        }

        $cmsBlockList = array(
            array(
                'version' => '2.1.4',
                'title' => 'Footer Service Links',
                'identifier' => 'footer_service_link',
                'store_id' => $borntechiesMainStore->getId(),
                'content' => <<<EOT
<ul class="footer-links logo-footer">
    <li>
        <img src="{{view url="images/logo.png"}}" alt="" />
    </li>
</ul>
<ul class="footer-links no-mobile">
    <li>
        <h3 class="footer-title">Quicklinks</h3>
    </li>
    <li>
        <a title="Shop" href="https://acc.borntechies.nl/">Shop</a>
    </li>
    <li>
        <a title="Service" href="{{store url="over_ons"}}">Service</a>
    </li>
    <li>
        <a title="Team" href="{{store url="team"}}">Team</a>
    </li>
    <li>
        <a title="Contact" href="{{store url="become-a-customer"}}">Klant worden</a>
    </li>
</ul>
<ul class="footer-links">
    <li>
        <h3 class="footer-title">Contact</h3>
    </li>
    <li class="info">{{config path="general/store_information/street_line1"}}
    </li>
    <li class="info">{{config path="general/store_information/postcode"}} {{config path="general/store_information/city"}}
    </li>
    <li class="info">{{config path="general/store_information/phone"}}
    </li>
    <li>
        <a href="mailto:{{config path="trans_email/ident_general/email"}}"> {{config path="trans_email/ident_general/email"}}</a>
    </li>
</ul>
<ul class="footer-links">
    <li>
        <h3 class="footer-title">Klant worden?</h3>
    </li>
    <li class="info">Direct bestellen voor de allerscherpste prijzen via ons bestel portal.
    </li>
    <li>
        <a class="footer-button" href="{{store url="contact"}}">neem contact op</a>
    </li>
</ul>
EOT
            ),array(
                'version' => '2.1.4',
                'title' => 'Borntechies main website top links',
                'identifier' => 'borntechies_top_links',
                'store_id' => $borntechiesMainStore->getId(),
                'content' => <<<EOT
<ul>
    <li class="level0 nav-1 level-top ui-menu-item">
        <a id="ui-id-1" class="level-top ui-corner-all" tabindex="-1" href="{{store url=""}}">Home</a>
    </li>
    <li class="level0 nav-2 level-top ui-menu-item">
        <a id="ui-id-2" class="level-top ui-corner-all" tabindex="-1" href="{{store url="team"}}">Team</a>
    </li>
    <li class="level0 nav-3 level-top ui-menu-item">
        <a id="ui-id-3" class="level-top ui-corner-all" tabindex="-1" href="{{store url="over_ons"}}">Over ons</a>
    </li>
    <li class="level0 nav-3 level-top ui-menu-item">
        <a id="ui-id-3" class="level-top ui-corner-all" tabindex="-1" href="{{store url="contact"}}">Contact</a>
    </li>
    <li class="level0 nav-3 level-top ui-menu-item desktop">
        <a class="level-top ui-corner-all" href="{{store url="become-a-customer"}}"><span class="contact-link"> <span>Klant worden?<span></span></span></span></a>
    </li>
</ul>
EOT
            ), array(
                'version' => '2.1.4',
                'title' => 'Borntechies main website account login link',
                'identifier' => 'borntechies_top_login',
                'store_id' => $borntechiesMainStore->getId(),
                'content' => <<<EOT
<div>
    <a href="https://acc.borntechies.nl/customer/account/login/">
        <span class="authorization-link"> 
            <span>Inloggen
                <span></span>
            </span>
        </span>
    </a>
    <a href="{{store url="become-a-customer"}}">
        <span class="contact-link">
            <span>Klant worden?
                <span></span>
            </span>
        </span>
    </a>
</div>
EOT
            ), array(
                'version' => '2.1.4',
                'title' => 'Homepage banner',
                'identifier' => 'homepage-banner',
                'store_id' => $borntechiesMainStore->getId(),
                'content' => <<<EOT
<div class="brands-logo">
    <div class="logos">Citroën, Peugeot, Renault, Dacia</div>
    <h3>Het originele alternatief</h3>
    <div class="home-banner-links">
        <img src="{{view url="images/icon_car.png"}}" alt="" />
        <a class="blue" href="https://acc.borntechies.nl/customer/account/login/"> 
            <span class="upper">Productcatalogus</span>
            <span>Zoeken en bestellen</span>
        </a>
    </div>
</div>
EOT
            ), array(
                'version' => '2.0.5',
                'title' => 'Mainwebsite contact page block',
                'identifier' => 'borntechies-contact-info',
                'store_id' => $borntechiesMainStore->getId(),
                'content' => <<<EOT
                <div class="half-block">
<h2 class="mobile-only">Adres</h2>
<div class="mapouter">
<div class="gmap_canvas">
<iframe id="gmap_canvas" src="https://maps.google.com/maps?q=TT%20Vasumweg%20112%201033%20SH%20Amsterdam&amp;t=&amp;z=13&amp;ie=UTF8&amp;iwloc=&amp;output=embed" width="320" height="240" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe></div>
</div>
<div class="contact-info">
<div class="half-block">
<p><strong>Borntechies</strong></p>
<p>Tt. Vasumweg 112</p>
<p>1033 SH Amsterdam</p>
<p>Nederland</p>
</div>
<div class="half-block contact-links">
<p><a href="tel:{{config path="general/store_information/phone"}}"><i class="fa fa-phone"></i> {{config path="general/store_information/phone"}}</a></p>
<p><i class="fa fa-fax"></i> +31(0)0206880625</p>
<p><a href="mailto:{{config path="trans_email/ident_general/email"}}"><i class="fa fa-envelope"></i> {{config path="trans_email/ident_general/email"}}</a></p>
</div>
<div class="clear"></div>
<div>
<table>
<tbody>
<tr><th width="50">VAT</th><th>NL 80 36 85 47 6 B01</th></tr>
<tr><th width="50">EORI</th><th>NL 80 36 85 47 6</th></tr>
<tr><th width="50">IBAN</th><th>NL 87 ABNA 055 85 22 084</th></tr>
<tr><th width="50">SWIFT</th><th>ABNANL2</th></tr>
</tbody>
</table>
</div>
</div>
</div>
EOT
            ), array(
                'version' => '2.1.4',
                'title' => 'Wholesale Text',
                'identifier' => 'wholesale-text',
                'store_id' => $borntechiesMainStore->getId(),
                'content' => <<<EOT
<div class="home-banner-links-mobile">
    <img src="{{view url="images/icon_car.png"}}" alt="" />
    <a class="blue" href="https://acc.borntechies.nl/customer/account/login/">
        <span class="upper">Productcatalogus</span>
        <span>Zoeken en bestellen</span> 
    </a>
</div>
<div class="home-middle-content">
    <div class="row">
        <div class="col-md-4 col-sm-4">
            <div class="images">
                <img src="{{view url="images/bestellen_chat.png"}}" alt="" />
            </div>
            <h4>Eenvoudig bestellen</h4>
            <div>Borntechies denkt graag met klanten mee. Daarom beschikt Borntechies over een webshop met chat functie, verschillende zoekfilters en een geavanceerde zoekmachine. Uiteraard kan er ook nog altijd telefonisch worden besteld.
            </div>
        </div>
        <div class="col-md-4 col-sm-4">
            <div class="images">
                <img src="{{view url="images/icon_fast_delivery.png"}}" alt="" />
            </div>
            <h4>Snelle levering</h4>
            <div>Artikelen die voor 17.00 uur zijn besteld, worden op de volgende werkdag door DHL bezorgd in de daglevering of door PartsExpress in de nachtlevering. Uw bestellingen worden door Borntechies met de grootste mogelijke zorg verwerkt.
            </div>
        </div>
        <div class="col-md-4 col-sm-4">
            <div class="images">
                <img src="{{view url="images/oil.png"}}" alt="" />
            </div>
            <h4>Correcte service</h4>
            <div>Borntechies staat garant voor de hoogste kwaliteit auto-onderdelen en service verlening. Met onze jarenlange ervaring en door de feedback van onze vele klanten weet Borntechies heel goed wat daarvoor nodig is. Wij streven er ten alle tijden naar om onze service naar een nog hoger niveau te tillen.
            </div>
        </div>
    </div>
</div>
EOT
            ), array(
                'version' => '2.0.2',
                'title' => 'carusell',
                'identifier' => 'carusell',
                'store_id' => $borntechiesMainStore->getId(),
                'content' => <<<EOT
                <div class="brands">
<div class="slic-images">
<div class="item"><img src="{{media url="wysiwyg/brands/image_1.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_2.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_3.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_4.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_5.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_6.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_7.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_8.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_9.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_10.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_11.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_12.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_13.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_14.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_15.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_16.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_17.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_18.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_19.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_20.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_21.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_22.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_23.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_24.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_25.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_26.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_27.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_28.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_29.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_30.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_31.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_32.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_33.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_34.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_35.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_36.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_37.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_38.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_39.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_40.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_41.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_42.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_43.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_44.jpg"}}" alt="" /></div>
<div class="item"><img src="{{media url="wysiwyg/brands/image_45.jpg"}}" alt="" /></div>
</div>
</div>
<script type="text/javascript" xml="space">// <![CDATA[
    require([
        'jquery',
        'slick'
    ], function ($) {
        jQuery(document).ready(function () {
            jQuery(".slic-images").slick({
                dots: false,
                infinite: true,
                speed: 300,
                slidesToShow: 8,
                slidesToScroll: 1,
  autoplay: true,
  autoplaySpeed: 2000,
                responsive: [
                    {
                        breakpoint: 1280,
                        settings: {
                            slidesToShow: 8,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 6,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 4,
                            slidesToScroll: 1
                        }
                    }
                ]
            });
        });
    });
// ]]></script>
EOT
            ), array(
                'version' => '2.1.4',
                'title' => 'Footer Service Link',
                'identifier' => 'footer_service_link',
                'store_id' => $borntechiesViewStore->getId(),
                'content' => <<<EOT
<ul class="footer-links logo-footer">
    <li>
        <a href="{{store url="customer/account"}}">
            <img src="{{view url="images/logo.png"}}" alt="Borntechies" />
        </a>
    </li>
</ul>
<ul class="footer-links">
    <li>
        <h3 class="footer-title">Service</h3>
    </li>
    <li>
        <a href="{{store url="downloads"}}">Downloads</a>
    </li>
    <li>
        <a title="Verkoopvoorwaarden" href="{{store url="verkoopvoorwaarden"}}">Verkoopvoorwaarden</a>
    </li>
    <li>
        <a title="Actiefolder" href="{{store url="actiefolder"}}">Actiefolder</a>
    </li>
    <li>
        <a title="Garantie en retour" href="{{store url="garantie-en-retour"}}">Garantie en retouren</a>
    </li>
</ul>
<ul class="footer-links">
    <li>
        <h3 class="footer-title">Quicklinks</h3>
    </li>
    <li>
        <a href="{{store url="catalogsearch/advanced"}}">Geavanceerd zoeken</a>
    </li>
    <li>
        <a href="{{store url="privacy-policy-cookie-policy"}}">Privacy Statement</a>
    </li>
    <li>
        <a href="{{store url="cookie-statement"}}">Cookie Statement</a>
    </li>
    <li>
        <a href="{{store url="contact"}}">Contact</a>
    </li>
</ul>
<ul class="footer-links">
    <li>
        <h3 class="footer-title">Mijn Borntechies</h3>
    </li>
    <li>
        <a href="{{store url="customer/account/login/"}}">Login</a>
    </li>
    <li>
        <a href="{{store url="customer/account/"}}">Mijn bestellingen</a>
    </li>
    <li>
        <a href="{{store url="/"}}">Shop</a>
    </li>
</ul>
<ul class="footer-links">
    <li>
        <h3 class="footer-title">Kunnen we u helpen?</h3>
    </li>
    <li class="info">Tt. Vasumweg 112</li>
    <li class="info">1033 SH Amsterdam</li>
    <li class="info">020 - 68 80 348</li>
    <li>
        <a href="mailto:info@borntechies.n">info@borntechies.nl</a>
    </li>
</ul>
EOT
            ),
        );

        $cmsPagesList = array(
            array(
                'version' => '2.1.4',
                'title' => 'Homepage',
                'page_layout' => '1column',
                'identifier' => 'home',
                'store_id' => $borntechiesMainStore->getId(),
                'content_heading' => '',
                'content' => <<<EOT
<div class="service">
    <div class="visitor">
        <div class="rows" id="counter">
            <div class="col-md-4 col-sm-4 counter">
                <h1 class="articles" data-count="50000">0</h1>
                <h4>verschillende artikelen</h4>
            </div>
            <div class="col-md-4 col-sm-4 counter">
                <h1 class="customer" data-count="98">0</h1>
                <h4>klanttevredenheid</h4>
            </div>
            <div class="col-md-4 col-sm-4 counter">
                <h1 data-count="34142">0</h1>
                <h4>tevreden klanten</h4>
            </div>
        </div>
    </div>
    <div class="service-labels">
        <img src="{{media url="wysiwyg/Foto_4.jpg"}}" alt="" />
        <img src="{{media url="wysiwyg/Foto_3.jpg"}}" alt="" />
        <img src="{{media url="wysiwyg/Foto_1.jpg"}}" alt="" />
        <img src="{{media url="wysiwyg/Foto_2.jpg"}}" alt="" />
    </div>
</div>
{{block class="Magento\Framework\View\Element\Template" template="Magento_Theme::counter.phtml"}}
EOT
            ), array(
                'version' => '2.1.4',
                'title' => 'Team',
                'store_id' => $borntechiesMainStore->getId(),
                'page_layout' => '1column',
                'identifier' => 'team',
                'content_heading' => '',
                'content' => <<<EOT
<div class="team">
    <div class="top-banner"></div>
    <h1 class="main-title">Team</h1>
    <div class="wrapper">
        <div class="box ">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_1.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Philippe van Rijmenam</span>
                <span class="role">directeur</span>
                <span class="contacts-info">
                    <span>
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 316 21 55 79 48">+31(0)6 21 55 79 48</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:Ph.van.Rijmenam@borntechies.nl">Ph.van.Rijmenam@borntechies.nl</a>
                    </span>
                    <span class="no-mobile">
                        <i class="fa fa-skype"></i>
                        <a href="skype:Philippe.van.Rijmenam?call">Philippe.van.Rijmenam</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_2.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Ted Amson</span>
                <span class="role">directeur</span>
                <span class="contacts-info">
                    <span>
                        <i class="fa fa-phone"></i>
                            <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 316 21 55 79 42">+31(0)6 21 55 79 42</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:T.Amson@borntechies.nl">T.Amson@borntechies.nl</a>
                    </span>
                    <span class="no-mobile">
                        <i class="fa fa-skype"></i>
                        <a href="skype:Ted.Amson?call">Ted.Amson</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_3.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Victor Diercks</span>
                <span class="role">export manager</span>
                <span class="contacts-info">
                    <span>
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 316 23 73 68 83">+31(0)6 23 73 68 83</a> 
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:V.Diercks@borntechies.nl">V.Diercks@borntechies.nl</a>
                    </span>
                    <span class="no-mobile">
                        <i class="fa fa-skype"></i>
                        <a href="skype:Victor.Diercks?call">Victor.Diercks</a> 
                    </span> 
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_5.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Alex van Rijmenam</span>
                <span class="role">export manager</span>
                <span class="contacts-info">
                    <span>
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 316 26 45 44 61">+31(0)6 26 45 44 61</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:A.v.Rijmenam@borntechies.nl">A.v.Rijmenam@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_6.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Bert Meijer</span>
                <span class="role">verkoop binnendienst</span>
                <span class="contacts-info">
                    <span>
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 316 21 55 79 41">+31(0)6 21 55 79 41</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:A.L.Meijer@borntechies.nl">A.L.Meijer@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_7.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Cees Jan Beerepoot</span>
                <span class="role">verkoop binnendienst</span>
                <span class="contacts-info"> 
                    <span>
                        <i class="fa fa-phone"></i>
                            <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                            <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                            <a href="mailto:C.Beerepoot@borntechies.nl">C.Beerepoot@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_8.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Luc Meijer</span>
                <span class="role">vertegenwoordiger nederland</span> 
                <span class="contacts-info"> 
                    <span> 
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                            <a href="tel:00 316 21 55 79 47">+31(0)6 21 55 79 47</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:L.Meijer@borntechies.nl">L.Meijer@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_9.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Charles Lassooy</span>
                <span class="role">vertegenwoordiger nederland</span>
                <span class="contacts-info">
                    <span>
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 316 21 55 79 46">+31(0)6 21 55 79 46</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:C.Lassooy@borntechies.nl">C.Lassooy@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_10.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Wies Sol</span>
                <span class="role">vertegenwoordiger benelux</span>
                <span class="contacts-info">
                    <span>
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 316 83 70 78 91">+31(0)6 83 70 78 91</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:W.Sol@borntechies.nl">W.Sol@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_5.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Hilke Meijers</span>
                <span class="role">verkoop binnendienst classics</span>
                <span class="contacts-info">
                    <span>
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 02 795">+31(0)20 68 02 795</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 3120 68 02 795">+31(0)20 68 02 795</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:H.Meijers@borntechies.nl">H.Meijers@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_5.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Carolien Jansen</span>
                <span class="role">financieel manager</span>
                <span class="contacts-info">
                    <span>
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 3120 68 02 794">+31(0)20 68 02 794</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:C.Jansen@borntechies.nl">C.Jansen@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_5.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Marlies Hoogendoorn</span>
                <span class="role">financieel manager</span>
                <span class="contacts-info"> 
                    <span>
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 3120 68 02 794">+31(0)20 68 02 794</a>
                    </span>
                    <span> 
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:M.Hoogendoorn@borntechies.nl">M.Hoogendoorn@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_4.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Dick van Eck</span>
                <span class="role">marketing manager</span>
                <span class="contacts-info">
                    <span>
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:D.van.Eck@borntechies.nl">D.van.Eck@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_5.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Caspar Amson</span>
                <span class="role">magazijn medewerker classics</span>
                <span class="contacts-info">
                    <span>
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 02 795">+31(0)20 68 02 795</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 3120 68 02 795">+31(0)20 68 02 795</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:info@borntechies.nl">info@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_13.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Michael Jordaan</span>
                <span class="role">magazijn medewerker</span>
                <span class="contacts-info">
                    <span>
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:info@borntechies.nl">info@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_15.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Saïd Zejli</span>
                <span class="role">magazijn medewerker</span>
                <span class="contacts-info">
                    <span>
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span> 
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:info@borntechies.nl">info@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_14.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Stephano Montnor</span>
                <span class="role">magazijn medewerker</span>
                <span class="contacts-info">
                    <span>
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:info@borntechies.nl">info@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_5.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Bryan de Lange</span>
                <span class="role">magazijn medewerker</span>
                <span class="contacts-info">
                    <span>
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:info@borntechies.nl">info@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
        <div class="box">
            <div class="image">
                <img src="{{media url="wysiwyg/team/person_5.jpg"}}" alt="" />
            </div>
            <div class="info">
                <span class="name">Jordy Stam</span>
                <span class="role">magazijn medewerker</span>
                <span class="contacts-info">
                    <span> 
                        <i class="fa fa-phone"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-mobile"></i>
                        <a href="tel:00 3120 68 80 348">+31(0)20 68 80 348</a>
                    </span>
                    <span>
                        <i class="fa fa-envelope"></i>
                        <a href="mailto:info@borntechies.nl">info@borntechies.nl</a>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>
EOT
            ), array(
        'version' => '2.0.9',
        'title' => 'Contact',
        'page_layout' => '1column',
        'identifier' => 'vragen',
        'store_id' => $borntechiesMainStore->getId(),
        'content_heading' => '',
        'content' => <<<EOT
<div class="contact-vragen">
<div class="contact-vragen-text">
<h3>Neem contact met op ons</h3>
<p>Neem contact op met 
<a href="mailto:{{config path="trans_email/ident_general/email"}}">
<i class="fa fa-envelope"></i> {{config path="trans_email/ident_general/email"}}
</a> 
voor vragen of via de chat rechtsonderin de pagina.</p>
<br />
</div>
</div>
EOT
    ), array(
                'version' => '2.1.4',
                'title' => 'Over Ons',
                'page_layout' => '1column',
                'identifier' => 'over_ons',
                'store_id' => $borntechiesMainStore->getId(),
                'content_heading' => '',
                'content' => <<<EOT
<div class="over-ons-banner-main"></div>
<div class="row">
    <div class="col-md-6 col-sm-6">
        <div class="onze-services">
            <h2>Onze services</h2>
            <p>Borntechies is een groothandel gespecialiseerd in kwaliteitsonderdelen voor Citroën, Peugeot, Renault en Dacia. Wij onderscheiden ons door een goed inzicht van actuele.</p>
        </div>
        <div class="desktop-cont">
            <div class="service-content"><img src="{{view url="images/Picture_1.jpg"}}" alt="" />
                <p><b>Sed ut perspiciatis</b> unde omnis iste natus error sit voluptatem accusantium doloremque lau-dantium, totam rem aperiam, eaque ipsa quae ab illo inven-tore veritatis et.</p>
            </div>
            <div class="service-content"><img src="{{view url="images/picture_2.jpg"}}" alt="" />
                <p><b>quasi architecto</b> beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione volup-tatem sequi nesciunt.</p>
            </div>
        </div>
        <div class="mobile-cont">
            <div class="images"><img src="{{view url="images/Picture_1.jpg"}}" alt="" />
                <img class="second" src="{{view url="images/picture_2.jpg"}}" alt="" />
            </div>
            <div class="service-content">
                <p><b>Sed ut perspiciatis</b> unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et.</p>
            </div>
            <div class="service-content">
                <p><b>quasi architecto</b> beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6">
        <h2 class="autohandelaren-title">Dé groothandel voor garages en autohandelaren</h2>
        <div class="autohandelaren">
            <p>Borntechies is een groothandel gespecialiseerd in kwaliteitsonderdelen voor Citroën, Peugeot, Renault en Dacia. Wij onderscheiden ons door een goed inzicht van actuele OE</p>
        </div>
        <div class="autohandelaren">
            <p class="text">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
            <p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet.</p>
        </div>
        <div class="autohandelaren">
            <p class="text">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
            <p>Borntechies is een groothandel gespecialiseerd in kwaliteitsonderdelen voor Citroën, Peugeot, Renault en Dacia.</p>
        </div>
    </div>
</div>
EOT
            ),
        );

        foreach ($cmsBlockList as $item) {
            if (version_compare($context->getVersion(), $item['version']) < 0) {
                //try {
                // Use model to load block since repository doesn't take into account store id
                // rewrite using repository after m 2.3 (issue fixed in 2.3 according to github bug log)
                $block = $this->blockFactory->create();
                $block->setStoreId($item['store_id'] ?? Store::DEFAULT_STORE_ID);

                $block->load($item['identifier'], 'identifier');
                if (!$block->getId()) {
                    $block->setIdentifier($item['identifier']);
                }

                $block->setTitle($item['title']);
                $block->setContent($item['content']);
                $block->setIsActive(1);
                $block->setStores(array($item['store_id'] ?? Store::DEFAULT_STORE_ID));
//                } catch (\Exception $e) {
//                    $block = $this->blockFactory->create();
//                    $block->addData([
//                        'title' => $item['title'],
//                        'identifier' => $item['identifier'],
//                        'content' => $item['content'],
//                        'is_active' => 1,
//                        'stores' => [$item['store_id'] ?? Store::DEFAULT_STORE_ID]
//                    ]);
//                }
                $this->blockRepository->save($block);
            }
        }

        /**
         * Insert pages
         */
        foreach ($cmsPagesList as $item) {
            if (version_compare($context->getVersion(), $item['version']) < 0) {
                $page = $this->pageFactory->create();
                $page->setStoreId($item['store_id'] ?? Store::DEFAULT_STORE_ID);

                $page->load($item['identifier'], 'identifier');
                if (!$page->getId()) {
                    $page->setIdentifier($item['identifier']);
                }

                $page->setTitle($item['title']);
                $page->setContent($item['content']);
                $page->setPageLayout($item['page_layout']);
                $page->setIsActive(1);
                $page->setStores(array($item['store_id'] ?? Store::DEFAULT_STORE_ID));

//                try {
//                    $page = $this->pageRepository->getById($item['identifier']);
//                    $page->setTitle($item['title']);
//                    $page->setContent($item['content']);
//                    $page->setLayoutUpdateXml(isset($item['layout_update_xml']) ? $item['layout_update_xml'] : '');
//                }
//                catch (\Exception $e) {
//                    $page = $this->pageFactory->create();
//                    $page->addData([
//                        'title' => $item['title'],
//                        'identifier' => $item['identifier'],
//                        'page_layout' => $item['page_layout'],
//                        'content' => $item['content'],
//                        'is_active' => 1,
//                        'stores' => array(0),
//                        'sort_order' => 0
//                    ]);
//                }
                $this->pageRepository->save($page);
            }
        }
    }
}
