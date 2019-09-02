<?php
namespace Borntechies\LicensePlate\Block\Adminhtml\Model\Edit\Button;

/**
 * Class Back
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Back extends Generic
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/*/')),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
}
