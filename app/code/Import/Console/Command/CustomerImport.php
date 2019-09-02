<?php
namespace Borntechies\Import\Console\Command;

use Borntechies\Import\Api\QueueManagementInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Indexer\Model\IndexerFactory;
use Borntechies\Import\Helper\Data as BorntechiesHelper;
use Borntechies\Import\Model\Import\Customer as CustomerImportModel;

/**
 * Class CustomerImport
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class CustomerImport extends AbstractImport
{
    /**
     * @var IndexerFactory
     */
    protected $indexerFactory;

    /**
     * @param AppState $appState
     * @param ObjectManagerInterface $manager
     * @param QueueManagementInterface $queueManagement
     * @param ScopeConfigInterface $scopeConfig
     * @param BorntechiesHelper $borntechiesHelper
     * @param IndexerFactory $indexerFactory
     */
    public function __construct(
        AppState $appState,
        ObjectManagerInterface $manager,
        QueueManagementInterface $queueManagement,
        ScopeConfigInterface $scopeConfig,
        BorntechiesHelper $borntechiesHelper,
        IndexerFactory $indexerFactory
    ) {
        parent::__construct($appState, $manager, $queueManagement, $scopeConfig, $borntechiesHelper);

        $this->indexerFactory = $indexerFactory;
    }

    /**
     * @var CustomerImportModel
     */
    protected $customerImport;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('borntechies:import:customers')
            ->setDescription('Run customer import');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode(Area::AREA_ADMINHTML);
        try {
            $this->customerImport = $this->manager->get(CustomerImportModel::class);
            $this->queueManagement->addReport(BorntechiesHelper::TYPE_CUSTOMER);
            $this->customerImport->import();
            $this->queueManagement->updateReport($this->customerImport->getFormatedLogTrace(), BorntechiesHelper::STATUS_SUCCESS);
            $indexer = $this->indexerFactory->create();
            $indexer->load("customer_grid");
            $indexer->reindexAll();
        } catch (\Exception $e) {
            $message = $this->customerImport->getFormatedLogTrace().PHP_EOL.$e->getMessage();
            $this->queueManagement->invalidateReport($message);
            $output->writeln("Error: $message");

            if ($this->scopeConfig->isSetFlag(BorntechiesHelper::PATH_SEND_EMAIL_ON_FAILURE) &&
                $this->scopeConfig->getValue(BorntechiesHelper::PATH_EMAIL_TO)
            ) {
                $output->writeln("Sending email...");
                $this->borntechiesHelper->sendErrorEmail($message);
            }
        }
        $output->writeln("<info>Finished Customer Import</info>");
    }
}