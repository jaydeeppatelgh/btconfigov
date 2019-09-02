<?php
namespace Borntechies\Import\Model\Import;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Upsell
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Upsell extends ImportAbstract implements ImportInterface
{
    /**
     * {@inheritdoc}
     */
    public function import()
    {
        $this->addMessage(__('Start upsell product import'));
        $import = $this->rapidFlowHelper->run($this->borntechiesHelper->getUpsellImportProfile());

        $this->addMessage(__('Import upsell products to update'));
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
            throw new LocalizedException(__('Error during upsell product import'));
        }
    }
}
