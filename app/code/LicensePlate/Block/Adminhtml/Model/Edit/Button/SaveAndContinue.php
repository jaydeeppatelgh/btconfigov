<?php
namespace Borntechies\LicensePlate\Block\Adminhtml\Model\Edit\Button;

/**
 * Class SaveAndContinue
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class SaveAndContinue extends Generic
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order' => 80,
        ];
    }
}
