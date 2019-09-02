<?php
namespace Borntechies\LicensePlate\Block\Adminhtml\Model\Edit\Button;

/**
 * Class Save
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Save extends Generic
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save Model'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
