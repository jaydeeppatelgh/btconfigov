<?php
namespace Borntechies\LicensePlate\Ui\DataProvider\Model\Form\Modifier;

use Borntechies\LicensePlate\Api\ModelRegistrationRepositoryInterface;
use Magento\Framework\Registry;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\DataType\Text;

/**
 * Class Registrations
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Registrations extends  AbstractModifier
{
    /**#@+
     * Group values
     */
    const GROUP_REGISTRATION_NAME = 'registration';
    const GROUP_REGISTRATION_SCOPE = 'data.licenseplate_model';
    const DATA_SCOPE = '';
    /**#@-*/

    /**#@+
     * Button values
     */
    const BUTTON_ADD = 'button_add';
    /**#@-*/

    /**#@+
     * Container values
     */
    const CONTAINER_HEADER_NAME = 'container_header';
    const CONTAINER_OPTION = 'container_option';
    const CONTAINER_COMMON_NAME = 'container_common';
    /**#@-*/

    /**#@+
     * Grid values
     */
    const GRID_REGISTRATIONS_NAME = 'registrations';
    /**#@-*/

    /**#@+
     * Field values
     */
    const FIELD_OPTION_ID = 'id';
    const FIELD_TITLE_NAME = 'registration';
    const FIELD_SORT_ORDER_NAME = 'sort_order';
    const FIELD_IS_DELETE = 'is_delete';
    /**#@-*/

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var ModelRegistrationRepositoryInterface
     */
    protected $registrationRepository;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @param Registry $registry
     * @param ModelRegistrationRepositoryInterface $modelRegistration
     */
    public function __construct(
        Registry $registry,
        ModelRegistrationRepositoryInterface $modelRegistration
    ) {
        $this->coreRegistry = $registry;
        $this->registrationRepository = $modelRegistration;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $registrations = [];

        if (!($model = $this->coreRegistry->registry('license_plate_model'))) {
            return $data;
        }
        if (!$model->getId()) {
            return $data;
        }
        $modelRegistrations = $this->registrationRepository->getList($model) ?: [];

        /** @var \Borntechies\LicensePlate\Model\ModelRegistration $registration */
        foreach ($modelRegistrations as $registration) {
            $registrations[] = $registration->getData();
        }

        return array_replace_recursive(
            $data,
            [
                $model->getId() => [
                    static::DATA_SOURCE_DEFAULT => [
                         static::GRID_REGISTRATIONS_NAME => $registrations
                    ]
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $this->createCustomOptionsPanel();

        return $this->meta;
    }

    /**
     * Create "Registrations" panel
     *
     * @return $this
     */
    protected function createCustomOptionsPanel()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                static::GROUP_REGISTRATION_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Registrations'),
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::GROUP_REGISTRATION_SCOPE,
                                'collapsible' => true,
                                'sortOrder' => 30
                            ],
                        ],
                    ],
                    'children' => [
                        static::CONTAINER_HEADER_NAME => $this->getHeaderContainerConfig(10),
                        static::GRID_REGISTRATIONS_NAME => $this->getOptionsGridConfig(20)
                    ]
                ]
            ]
        );

        return $this;
    }

    /**
     * Get config for header container
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getHeaderContainerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => 'ui/form/components/complex',
                        'sortOrder' => $sortOrder,
                        'content' => __('Registration let customer choose the model to show.'),
                    ],
                ],
            ],
            'children' => [
                static::BUTTON_ADD => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'title' => __('Add Registration'),
                                'formElement' => Container::NAME,
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/form/components/button',
                                'sortOrder' => 20,
                                'actions' => [
                                    [
                                        'targetName' => 'ns = ${ $.ns }, index = ' . static::GRID_REGISTRATIONS_NAME,
                                        'actionName' => 'processingAddChild',
                                    ]
                                ]
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for the whole grid
     *
     * @param int $sortOrder
     *
     * @return array
     */
    protected function getOptionsGridConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel' => __('Add Registration'),
                        'componentType' => DynamicRows::NAME,
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows',
                        'template' => 'ui/dynamic-rows/templates/collapsible',
                        'additionalClasses' => 'admin__field-wide',
                        'deleteProperty' => static::FIELD_IS_DELETE,
                        'deleteValue' => '1',
                        'addButton' => false,
                        'renderDefaultRecord' => false,
                        'columnsHeader' => false,
                        'collapsibleHeader' => true,
                        'sortOrder' => $sortOrder,
                        "dndConfig" => ["enabled" => false],
                        'links' => ['insertData' => '${ $.provider }:${ $.dataProvider }'],
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'headerLabel' => __('New Registration'),
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'isTemplate' => true,
                                'is_collection' => true,
                            ],
                        ],
                    ],
                    'children' => [
                        static::CONTAINER_OPTION => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Fieldset::NAME,
                                        'label' => null,
                                        'sortOrder' => 10,
                                        'opened' => false,
                                    ],
                                ],
                            ],
                            'children' => [
                                static::CONTAINER_COMMON_NAME => $this->getCommonContainerConfig(10)
                            ]
                        ],
                    ]
                ]
            ]
        ];
    }

    /**
     * Get config for container with common fields for any type
     *
     * @param int $sortOrder
     *
     * @return array
     */
    protected function getCommonContainerConfig($sortOrder)
    {
        $commonContainer = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Container::NAME,
                        'formElement' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/group',
                        'breakLine' => false,
                        'showLabel' => false,
                        'additionalClasses' => 'admin__field-group-columns admin__control-group-equal',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                static::FIELD_OPTION_ID => $this->getRegistrationIdFieldConfig(10),
                static::FIELD_TITLE_NAME => $this->getTitleFieldConfig(
                    20,
                    [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => __('Registration Title'),
                                    'component' => 'Magento_Catalog/component/static-type-input',
                                    'valueUpdate' => 'input',
                                    'imports' => [
                                        'registrationId' => '${ $.provider }:${ $.parentScope }.id'
                                    ]
                                ],
                            ],
                        ],
                    ]
                )
            ]
        ];

        return $commonContainer;
    }

    /**
     * Get config for hidden id field
     *
     * @param int $sortOrder
     *
     * @return array
     */
    protected function getRegistrationIdFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Input::NAME,
                        'componentType' => Field::NAME,
                        'dataScope' => static::FIELD_OPTION_ID,
                        'sortOrder' => $sortOrder,
                        'visible' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for "Title" fields
     *
     * @param int $sortOrder
     * @param array $options
     *
     * @return array
     */
    protected function getTitleFieldConfig($sortOrder, array $options = [])
    {
        return array_replace_recursive(
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Title'),
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataScope' => static::FIELD_TITLE_NAME,
                            'dataType' => Text::NAME,
                            'sortOrder' => $sortOrder,
                            'validation' => [
                                'required-entry' => true
                            ],
                        ],
                    ],
                ],
            ],
            $options
        );
    }
}