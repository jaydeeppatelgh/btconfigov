<?php
namespace Borntechies\Import\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Borntechies\Import\Helper\Data as BorntechiesHelper;
use Borntechies\Import\Model\Import\Upsell as UpsellImportModel;

/**
 * Class UpsellImport
 *
 *  @author      Anil <anil.shah@borntechies.com>
 */
class UpsellImport extends AbstractImport
{
    /**
     * @var UpsellImportModel
     */
    protected $upsellImport;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('borntechies:import:upsell')
            ->setDescription('Run upsell product import');
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
            $this->upsellImport = $this->manager->get(UpsellImportModel::class);
            $this->queueManagement->addReport(BorntechiesHelper::TYPE_UPSELL);
            $this->upsellImport->import();
            $this->queueManagement->updateReport($this->upsellImport->getMessage(), BorntechiesHelper::STATUS_SUCCESS);
        } catch (\Exception $e) {
            $message = $this->upsellImport->getMessage().PHP_EOL.$e->getMessage();
            $this->queueManagement->invalidateReport($message);
            $output->writeln("Error: $message");

            if ($this->scopeConfig->isSetFlag(BorntechiesHelper::PATH_SEND_EMAIL_ON_FAILURE) &&
                $this->scopeConfig->getValue(BorntechiesHelper::PATH_EMAIL_TO)
            ) {
                $output->writeln("Sending email...");
                $this->borntechiesHelper->sendErrorEmail($message);
            }
        }
        $output->writeln("<info>Finished Upsell Product Import</info>");
    }
}