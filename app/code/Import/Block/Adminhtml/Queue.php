<?php
namespace Borntechies\Import\Block\Adminhtml;

use Borntechies\Import\Helper\Data as BorntechiesHelper;

/**
 * Class Queue
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Queue extends \Magento\Backend\Block\Widget\Container
{
    /**
     * Prepare button and grid
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id' => 'run_import',
            'label' => __('Run Import'),
            'title' => __('Run Import'),
            'class' => 'add',
            'button_class' => '',
            'class_name' => 'Magento\Backend\Block\Widget\Button\SplitButton',
            'options' => $this->getAddImportButtonOptions(),
        ];
        $this->buttonList->add('run_import', $addButtonProps);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve options for 'Run Import' split button
     *
     * @return array
     */
    protected function getAddImportButtonOptions()
    {
        $splitButtonOptions = [];

        foreach (BorntechiesHelper::getAvailableTypes() as $type) {
            $splitButtonOptions[$type] = [
                'label' => __($this->getActionLabel($type)),
                'onclick' => "setLocation('" . $this->getActionUrl($type) . "')",
            ];
        }

        return $splitButtonOptions;
    }

    /**
     * Retrieve import label
     *
     * @param string $type
     *
     * @return string
     */
    protected function getActionLabel($type)
    {
        switch ($type) {
            case BorntechiesHelper::TYPE_PRODUCT:  return __('Import Products');
            case BorntechiesHelper::TYPE_CUSTOMER: return __('Import Customers');
            case BorntechiesHelper::TYPE_PRICE:    return __('Import Prices');
            case BorntechiesHelper::TYPE_UPSELL:   return __('Import Upsell Products');
            case BorntechiesHelper::TYPE_CATEGORY: return __('Import Categories');
            case BorntechiesHelper::TYPE_STOCK:    return __('Import Stock');
        }

        return '';
    }

    /**
     * Retrieve import run url by specified type
     *
     * @param string $type
     *
     * @return string
     */
    protected function getActionUrl($type)
    {
        return $this->getUrl('*/*/import', ['type' => $type]);
    }
}
