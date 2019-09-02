<?php
namespace Borntechies\OrderExport\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Borntechies\OrderExport\Model\Export\Order as OrderExporter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class OrderExport
 * @package Borntechies\OrderExport\Console\Command
 */
class OrderExport extends Command
{
    const PATH_EXPORT_DIR = 'borntechies/general/output_dir';

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var OrderExporter
     */
    private $exporter;

    /**
     * Order whose data is exported
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    private $orderCollection;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param OrderExporter $exporter
     * @param AppState $appState
     * @param CollectionFactory $orderColFactory
     * @param DirectoryList $directoryList
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        OrderExporter $exporter,
        AppState $appState,
        CollectionFactory $orderColFactory,
        DirectoryList $directoryList,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct();

        $this->exporter = $exporter;
        $this->appState = $appState;
        $this->orderCollection = $orderColFactory->create();
        $this->directoryList = $directoryList;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('borntechies:export:orders')
            ->setDescription('Run order export');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode(Area::AREA_ADMINHTML);

        try {
            $ordersToExport = $this->orderCollection->addFieldToFilter('is_exported', 0);
            foreach ($ordersToExport as $order) {
                $itemsExported = $this->exporter->exportOrder($order);
                $filename = $this->exporter->getFileName($order);
                rename($this->directoryList->getPath(DirectoryList::VAR_DIR).DIRECTORY_SEPARATOR.\Borntechies\OrderExport\Model\Export\Order::EXPORT_DIR.DIRECTORY_SEPARATOR.$filename,
                    $this->scopeConfig->getValue(self::PATH_EXPORT_DIR).DIRECTORY_SEPARATOR.$filename);
                $order->setIsExported(1);
                $order->addStatusHistoryComment( "Successfully exported to file {$filename}");
                $order->save();
                $output->writeln("New order  {$order->getIncrementId()} ...");
                $output->writeln("{$itemsExported} articles found..");
            }
            $output->writeln("<info>Finished Order Export</info>");
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}