<?php
namespace Borntechies\LicensePlate\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Borntechies\Import\Helper\Data as ImportHelper;
use Borntechies\LicensePlate\Model\Import;

/**
 * Class PriceImport
 *
 *  @author      Anil <anil.shah@borntechies.com>
 */
class LicenseplateImport extends \Borntechies\Import\Console\Command\AbstractImport
{
    /**
     * @var Import
     */
    protected $import;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('borntechies:import:licenseplate')
            ->setDescription('Run License plate import');
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
            /** @var Import import */
            $this->import = $this->manager->get(Import::class);
            $this->queueManagement->addReport(ImportHelper::TYPE_LICENSE_PLATE);
            $this->import->import();
            $this->queueManagement->updateReport($this->import->getErrorMessage(), ImportHelper::STATUS_SUCCESS);
        } catch (\Exception $e) {
            $message = $this->import->getErrorMessage().PHP_EOL.$e->getMessage();
            $this->queueManagement->invalidateReport($message);
            $output->writeln("Error: $message");

            if ($this->scopeConfig->isSetFlag(ImportHelper::PATH_SEND_EMAIL_ON_FAILURE) &&
                $this->scopeConfig->getValue(ImportHelper::PATH_EMAIL_TO)
            ) {
                $output->writeln("Sending email...");
                $this->borntechiesHelper->sendErrorEmail($message);
            }
        }
        $output->writeln("<info>Finished Licenseplate Import</info>");
    }
}