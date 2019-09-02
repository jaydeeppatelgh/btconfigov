<?php
namespace Borntechies\LicensePlate\Ui\DataProvider\Model\Form\Modifier;

use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Borntechies\LicensePlate\Api\Data\ModelInterface;
use Magento\Framework\Registry;
use Borntechies\LicensePlate\Api\ModelRepositoryInterface;
use Magento\Framework\UrlInterface;

/**
 * Class Details
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Details extends  AbstractModifier
{
    const KEY_SUBMIT_URL = 'submit_url';
    const KEY_RELOAD_URL = 'reloadUrl';

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var ModelRepositoryInterface
     */
    protected $modelRepository;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var array
     */
    protected $modelUrls = [
        self::KEY_SUBMIT_URL => 'licenseplate/model/save',
        self::KEY_RELOAD_URL => 'licenseplate/model/reload'
    ];

    protected $requiredFields = [
        ModelInterface::HMDNR,
        ModelInterface::MAKE,
        ModelInterface::MODEL,
        ModelInterface::FUEL,
        ModelInterface::MODEL,
            ];

    /**
     * Details constructor.
     *
     * @param Registry                 $registry
     * @param ModelRepositoryInterface $modelRepository
     * @param UrlInterface             $urlBuilder
     * @param array                    $modelUrls
     */
    public function __construct(
        Registry $registry,
        ModelRepositoryInterface $modelRepository,
        UrlInterface $urlBuilder,
        array $modelUrls = []
    ) {
        $this->coreRegistry = $registry;
        $this->modelRepository = $modelRepository;
        $this->urlBuilder = $urlBuilder;
        $this->modelUrls = array_replace_recursive($this->modelUrls, $modelUrls);
    }

    const SORT_ORDER_MULTIPLIER = 10;

    /**
     * @param array $data
     *
     * @return array
     */
    public function modifyData(array $data)
    {
        $submitUrl = $this->urlBuilder->getUrl($this->modelUrls[self::KEY_SUBMIT_URL]);
        $reloadUrl = $this->urlBuilder->getUrl($this->modelUrls[self::KEY_RELOAD_URL]);

        if ($model = $this->coreRegistry->registry('license_plate_model')) {
            if (!$model->getId()) {
                return $data;
            }

            foreach ($this->getFields() as $code => $label) {
                $data[$model->getId()][self::DATA_SOURCE_DEFAULT][$code] = $model->getData($code);
            }
        }
        return array_replace_recursive(
            $data,
            [
                'config' => [
                    self::KEY_SUBMIT_URL => $submitUrl,
                    self::KEY_RELOAD_URL => $reloadUrl,
                ]
            ]
        );
    }

    /**
     * @param array $meta
     *
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $groupCode = 'model-details';

        $meta[$groupCode]['children'] = $this->getFieldSettings($this->getFields());
        $meta[$groupCode]['arguments']['data']['config']['componentType'] = Fieldset::NAME;
        $meta[$groupCode]['arguments']['data']['config']['label'] = __('%1', 'Model Details');
        $meta[$groupCode]['arguments']['data']['config']['collapsible'] = false;
        $meta[$groupCode]['arguments']['data']['config']['dataScope'] = self::DATA_SCOPE_MODEL;
        $meta[$groupCode]['arguments']['data']['config']['sortOrder'] = 10;

        return $meta;
    }

    /**
     * Model Form fields
     *
     * @return array
     */
    protected function getFields()
    {
        return [
            ModelInterface::HMDNR => 'HMDNR',
            ModelInterface::MAKE => 'Manufacturer',
            ModelInterface::MODEL => 'Model',
            ModelInterface::FUEL => 'Fuel',
            ModelInterface::MOTOR_CODE => 'Motor Code',
            ModelInterface::GENERATION => 'Generation',
            ModelInterface::CONSTRUCTION_PERIOD => 'Construction Period',
            ModelInterface::INTRODUCTION_DATE => 'Introduction Date'
        ];
    }

    /**
     * Get form field settings
     *
     * @param array $fields
     *
     * @return array
     */
    protected function getFieldSettings($fields)
    {
        $fieldsSettings = [];
        $sortOrder = 0;
        foreach ($fields as $field => $label) {
            $fieldsSettings['container_'.$field] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement' => 'container',
                            'componentType' => 'container',
                            'breakLine' => false,
                            'label' => $label,
                            'required' => false,
                            'scopeLabel' => __($label),
                            'sortOrder' => $sortOrder * self::SORT_ORDER_MULTIPLIER
                        ]
                    ]
                ],
                'children' => [
                    $field => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataType' => 'text',
                                    'formElement' => 'input',
                                    'visible' => 1,
                                    'required' => 0,
                                    'notice' => null,
                                    'default' => null,
                                    'label' => $label,
                                    'code' => $field,
                                    'source' => 'model-details',
                                    'scopeLabel' => __($label),
                                    'sortOrder' => $sortOrder * self::SORT_ORDER_MULTIPLIER,
                                    'componentType' =>  Field::NAME,
                                    'validation' => in_array($field, $this->requiredFields) ? ['required-entry' => true] : []
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            $sortOrder++;
        }

        return $fieldsSettings;
    }
}