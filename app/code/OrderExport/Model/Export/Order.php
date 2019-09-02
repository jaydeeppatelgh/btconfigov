<?php
namespace Borntechies\OrderExport\Model\Export;

/**
 * Class Order
 * @package Borntechies\OrderExport\Model\Export
 */
class Order {
    const PATH_DELIMITE = 'borntechies/general/csv_separator';
    const PATH_ENCLOSURE = 'borntechies/general/csv_enclosure';

    const EXPORT_DIR = 'export/orders';

    const COLUMN_NAME = 'debiteurnr';
    const COLUMN_ORDERID = 'ordernr';
    const COLUMN_QTY = 'aantal';
    const COLUMN_CREATEDAT = 'datum';
    const COLUMN_SKU = 'artikelnr';
    const COLUMN_COMMENT = 'memo';

    protected $headerColumns = [
        self::COLUMN_NAME,
        self::COLUMN_ORDERID,
        self::COLUMN_QTY,
        self::COLUMN_CREATEDAT,
        self::COLUMN_SKU,
        self::COLUMN_COMMENT
    ];

    /**
     * @var \Borntechies\OrderExport\Model\Export\Adapter\Factory
     */
    protected $exportAdapterFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var string
     */
    protected $writeAdapter;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var int
     */
    protected $importedItemsQty;

    /**
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     * @param Adapter\Factory $exportAdapterFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param string $writeAdapter
     * @param string $format
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Borntechies\OrderExport\Model\Export\Adapter\Factory $exportAdapterFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        $writeAdapter,
        $format,
        array $data = []
    ) {
        $this->timezoneInterface = $timezoneInterface;
        $this->exportAdapterFactory = $exportAdapterFactory;
        $this->writeAdapter = $writeAdapter;
        $this->scopeConfig = $scopeConfig;
        $this->format = $format;
    }

    /**
     * Export process
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return int
     */
    public function exportOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        // create export file
        $writer = $this->exportAdapterFactory->create($this->writeAdapter, ['destination' => self::EXPORT_DIR.DIRECTORY_SEPARATOR.$this->getFileName($order)]);
        $writer->setDelimiter($this->scopeConfig->getValue(self::PATH_DELIMITE));
        $writer->setEnclosure($this->scopeConfig->getValue(self::PATH_ENCLOSURE));
        $writer->setHeaderCols($this->_getHeaderColumns());
        $this->importedItemsQty = 0;

        foreach ($order->getAllVisibleItems() as $item) {
            $row = [
                self::COLUMN_NAME => $order->getCustomerFirstname().' '.$order->getCustomerLastname(),
                self::COLUMN_ORDERID => $order->getIncrementId(),
                self::COLUMN_QTY => $item->getQtyOrdered(),
                self::COLUMN_CREATEDAT => $this->formatOrderDate($order),
                self::COLUMN_SKU => $item->getSku(),
                self::COLUMN_COMMENT => $order->getOrderComments()
            ];
            $this->importedItemsQty++;
            $writer->writeRow($row);
        }
        return $this->importedItemsQty;
    }

    /**
     * Format date for an export
     *
     * @param string \Magento\Sales\Api\Data\OrderInterface $order
     * @return bool|string
     */
    protected function formatOrderDate($order)
    {
        $created = $order->getCreatedAt();

        $created = $this->timezoneInterface->date(new \DateTime($created));

        return $created->format("d-m-Y H:i:s");
    }

    /**
     * Get file name for order
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return string
     */
    public function getFileName(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        return preg_replace( "/[^a-z0-9_-]+/i", "_", $order->getIncrementId()).'.'. $this->format;
    }

    /**
     * Get header columns for the file
     *
     * @return array
     */
    public function _getHeaderColumns()
    {
        return $this->headerColumns;
    }
}