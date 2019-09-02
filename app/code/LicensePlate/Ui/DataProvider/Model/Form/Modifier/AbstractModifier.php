<?php
namespace Borntechies\LicensePlate\Ui\DataProvider\Model\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class AbstractModifier
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
abstract class AbstractModifier implements ModifierInterface
{
    const FORM_NAME = 'licenseplate_model_form';
    const DATA_SOURCE_DEFAULT = 'licenseplate_model';
    const DATA_SCOPE_MODEL= 'data.licenseplate_model';

    /**
     * Name of default general panel
     */
    const DEFAULT_GENERAL_PANEL = 'model-details';

    /**
     * Return name of first panel (general panel)
     *
     * @param array $meta
     * @return string
     */
    protected function getGeneralPanelName(array $meta)
    {
        if (!$meta) {
            return null;
        }

        if (isset($meta[self::DEFAULT_GENERAL_PANEL])) {
            return self::DEFAULT_GENERAL_PANEL;
        }

        return $this->getFirstPanelCode($meta);
    }

    /**
     * Retrieve first panel name
     *
     * @param array $meta
     * @return string|null
     */
    protected function getFirstPanelCode(array $meta)
    {
        $min = null;
        $name = null;

        foreach ($meta as $fieldSetName => $fieldSetMeta) {
            if (
                isset($fieldSetMeta['arguments']['data']['config']['sortOrder'])
                && (null === $min || $fieldSetMeta['arguments']['data']['config']['sortOrder'] <= $min)
            ) {
                $min = $fieldSetMeta['arguments']['data']['config']['sortOrder'];
                $name = $fieldSetName;
            }
        }

        return $name;
    }
}