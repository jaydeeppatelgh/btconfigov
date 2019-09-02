<?php
namespace Borntechies\Import\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;
use Borntechies\Import\Helper\Data as BorntechiesHelper;

/**
 * Class Status
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Status extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $class = '';
                $text = '';
                switch ($item['status']) {
                    case BorntechiesHelper::STATUS_ERROR:
                        $class = 'grid-severity-critical';
                        $text = __('Error');
                        break;
                    case BorntechiesHelper::STATUS_SCHEDULED:
                        $class = 'grid-severity-minor';
                        $text = __('Scheduled');
                        break;
                    case BorntechiesHelper::STATUS_RUNNING:
                        $class = 'grid-severity-minor';
                        $text = __('Running');
                        break;
                    case BorntechiesHelper::STATUS_SUCCESS:
                        $class = 'grid-severity-notice';
                        $text = __('Success');
                        break;
                }
                $item[$fieldName . '_html'] = "<span class='{$class}'>$text</span>";
            }
        }

        return $dataSource;
    }
}