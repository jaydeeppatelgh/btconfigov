<?php
namespace Borntechies\LicensePlate\Block\Adminhtml\Model\Edit\Button;

/**
 * Class Reset
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Reset extends Generic
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Reset'),
            'class' => 'reset',
            'on_click' => 'location.reload();',
            'sort_order' => 30
        ];
    }
}
