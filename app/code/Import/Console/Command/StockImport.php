<?php
namespace Borntechies\Import\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Borntechies\Import\Helper\Data as BorntechiesHelper;
use Borntechies\Import\Model\Import\Stock as StockImportModel;

/**
 * Class StockImport
 *
 *  @author      Anil <anil.shah@borntechies.com>
 */
class StockImport extends AbstractImport
{
    /**
     * @var StockImportModel
     */
    protected $stockImport;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('borntechies:import:stock')
            ->setDescription('Run product stock import');
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
            $this->stockImport = $this->manager->get(StockImportModel::class);
            $this->queueManagement->addReport(BorntechiesHelper::TYPE_STOCK);
            $this->stockImport->import();
            $this->queueManagement->updateReport($this->stockImport->getMessage(), BorntechiesHelper::STATUS_SUCCESS);
        } catch (\Exception $e) {
            $message = $this->stockImport->getMessage().PHP_EOL.$e->getMessage();
            $this->queueManagement->invalidateReport($message);
            $output->writeln("Error: $message");

            if ($this->scopeConfig->isSetFlag(BorntechiesHelper::PATH_SEND_EMAIL_ON_FAILURE) &&
                $this->scopeConfig->getValue(BorntechiesHelper::PATH_EMAIL_TO)
            ) {
                $output->writeln("Sending email...");
                $this->borntechiesHelper->sendErrorEmail($message);
            }
        }
        $output->writeln("<info>Finished Stock Import</info>");
    }
}