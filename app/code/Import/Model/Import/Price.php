<?php
namespace Borntechies\Import\Model\Import;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Price
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Price extends ImportAbstract implements ImportInterface
{
    /**
     * {@inheritdoc}
     */
    public function import()
    {
        $this->addMessage(__('Start price import'));
        $import = $this->rapidFlowHelper->run($this->borntechiesHelper->getPriceImportProfile());

        $this->addMessage(__('Import prices to update'));
        $this->addMessage(__('Rows Found %1', $import->getRowsFound()));
        $this->addMessage(__('Rows Processed %1', $import->getRowsProcessed()));
        $this->addMessage(__('Rows Successful %1', $import->getRowsSuccess()));
        $this->addMessage(__('Rows Depends %1', $import->getRowsDepends()));
        $this->addMessage(__('Rows Not Changed %1', $import->getRowsNochange()));
        $this->addMessage(__('Rows Empty/Comment %1', $import->getRowsEmpty()));
        $this->addMessage(__('Rows With Errors %1', $import->getRowsErrors()));
        $this->addMessage(__('Total Error %1', $import->getNumErrors()));
        $this->addMessage(__('Total Warnings %1', $import->getNumWarnings()));

        if ($import->getCurrentActivity() != __('Done')) {
            throw new LocalizedException(__('Error during price import'));
        }
    }
}
