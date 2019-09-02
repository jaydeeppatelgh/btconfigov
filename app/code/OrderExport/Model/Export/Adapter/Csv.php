<?php
namespace Borntechies\OrderExport\Model\Export\Adapter;
use Magento\ImportExport\Model\Export\Adapter\Csv as CsvAdapter;
/**
 * Class Csv
 * @package Borntechies\OrderExport\Model\Export\Adapter
 */
class Csv extends CsvAdapter
{
    /**
     * Set CSV file delimiter
     *
     * @param string $delimiter
     */
    public function setDelimiter($delimiter)
    {
        $this->_delimiter = $delimiter;
    }

    /**
     * Set CSV file enclosure
     *
     * @param string $enclosure
     */
    public function setEnclosure($enclosure)
    {
        $this->_enclosure = $enclosure;
    }
}
