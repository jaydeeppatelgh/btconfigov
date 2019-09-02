<?php
namespace Borntechies\Import\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Borntechies\Import\Helper\Data as BorntechiesHelper;
use Borntechies\Import\Model\Import\Price as PriceImportModel;

/**
 * Class PriceImport
 *
 *  @author      Anil <anil.shah@borntechies.com>
 */
class PriceImport extends AbstractImport
{
    /**
     * @var PriceImportModel
     */
    protected $priceImport;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('borntechies:import:prices')
            ->setDescription('Run price import');
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
            $this->priceImport = $this->manager->get(PriceImportModel::class);
            $this->queueManagement->addReport(BorntechiesHelper::TYPE_PRICE);
            $this->priceImport->import();
            $this->queueManagement->updateReport($this->priceImport->getMessage(), BorntechiesHelper::STATUS_SUCCESS);
        } catch (\Exception $e) {
            $message = $this->priceImport->getMessage().PHP_EOL.$e->getMessage();
            $this->queueManagement->invalidateReport($message);
            $output->writeln("Error: $message");

            if ($this->scopeConfig->isSetFlag(BorntechiesHelper::PATH_SEND_EMAIL_ON_FAILURE) &&
                $this->scopeConfig->getValue(BorntechiesHelper::PATH_EMAIL_TO)
            ) {
                $output->writeln("Sending email...");
                $this->borntechiesHelper->sendErrorEmail($message);
            }
        }
        $output->writeln("<info>Finished Price Import</info>");
    }
}