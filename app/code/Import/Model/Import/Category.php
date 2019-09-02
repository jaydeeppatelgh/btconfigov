<?php
namespace Borntechies\Import\Model\Import;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Category
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Category extends ImportAbstract implements ImportStoreInterface
{
    /**
     * {@inheritdoc}
     */
    public function import($storeCode = null)
    {
        if (empty($storeCode)) {
            $storeCode = \Magento\Store\Model\Store::ADMIN_CODE;
        }

        $this->addMessage(__('Start category import'));
        $import = $this->rapidFlowHelper->run($this->borntechiesHelper->getCategoryImportProfile($storeCode));

        $this->addMessage(__('Import categories to update'));
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
            throw new LocalizedException(__('Error during category import'));
        }
    }
}
