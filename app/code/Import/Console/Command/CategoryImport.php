<?php
namespace Borntechies\Import\Console\Command;

use Magento\Framework\App\Area;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Borntechies\Import\Helper\Data as BorntechiesHelper;
use Borntechies\Import\Model\Import\Category as CategoryImportModel;

/**
 * Class CategoryImport
 *
 *  @author      Anil <anil.shah@borntechies.com>
 */
class CategoryImport extends AbstractImport
{
    const STORE_CODE = 'store_code';

    /**
     * @var CategoryImportModel
     */
    protected $categoryImport;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::STORE_CODE,
                null,
                InputOption::VALUE_OPTIONAL,
                'Store Code'
            )
        ];
        $this->setName('borntechies:import:categories')
            ->setDescription('Run category import')
            ->setDefinition($options);
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
        $storeCode = $input->getOption(self::STORE_CODE);
        if (empty($storeCode)) {
            $storeCode = \Magento\Store\Model\Store::ADMIN_CODE;
        }

        $this->appState->setAreaCode(Area::AREA_ADMINHTML);
        try {
            $this->categoryImport = $this->manager->get(CategoryImportModel::class);
            $this->queueManagement->addReport(BorntechiesHelper::TYPE_CATEGORY);
            $this->categoryImport->import($storeCode);
            $this->queueManagement->updateReport($this->categoryImport->getMessage(), BorntechiesHelper::STATUS_SUCCESS);
        } catch (\Exception $e) {
            $message = $this->categoryImport->getMessage().PHP_EOL.$e->getMessage();
            $this->queueManagement->invalidateReport($message);
            $output->writeln("Error: $message");

            if ($this->scopeConfig->isSetFlag(BorntechiesHelper::PATH_SEND_EMAIL_ON_FAILURE) &&
                $this->scopeConfig->getValue(BorntechiesHelper::PATH_EMAIL_TO)
            ) {
                $output->writeln("Sending email...");
                $this->borntechiesHelper->sendErrorEmail($message);
            }
        }
        $output->writeln("<info>Finished Category Import</info>");
    }
}
