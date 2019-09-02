<?php
namespace Borntechies\Import\Model\Import;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Product
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Product extends ImportAbstract implements ImportStoreInterface
{
    /**
     * @param string $storeCode
     * @return void
     * @throws LocalizedException
     */
    public function import($storeCode = null)
    {
        if (empty($storeCode)) {
            $storeCode = \Magento\Store\Model\Store::ADMIN_CODE;
        }

        $this->addMessage(__('Start product import'));
        if ($this->borntechiesHelper->deletePreviousProducts($storeCode)) {
            $this->rapidFlowHelper->run($this->borntechiesHelper->getProductExportProfile());
            $this->addMessage(__('Export existing products'));

            $profileImport = $this->rapidFlowHelper->getProfile($this->borntechiesHelper->getProductImportProfile($storeCode));
            $newSkus = [];

            // the new products feed
            $profileImport->ioOpenRead();
            $header = $profileImport->ioRead();
            while ($r = $profileImport->ioRead()) {
                if (count($r) != count($header)) {
                    throw new LocalizedException(__('Import file is not valid. Please recheck and correct it.'));
                }
                $row = array_combine($header, $r);
                $newSkus[$row['sku']] = 1;
            }
            $profileImport->ioClose();

            // the existing products export
            $profileExport = $this->rapidFlowHelper->getProfile($this->borntechiesHelper->getProductExportProfile());
            $profileExport->ioOpenRead();
            $header = $profileExport->ioRead();
            if ($profileExport->getCurrentActivity() != __('Done')) {
                throw new LocalizedException(__('Error during product export'));
            }

            // new file to update products statuses
            $profileDelete = $this->rapidFlowHelper->getProfile($this->borntechiesHelper->getProductDeleteProfile());
            $profileDelete->ioOpenWrite();
            $profileDelete->ioWriteHeader(['#CP','sku']);

            $productsToDelete = [];
            while ($r = $profileExport->ioRead()) {
                $row = array_combine($header, $r);
                if (empty($newSkus[$row['sku']])) {
                    $productsToDelete[] = $row['sku'];
                    $profileDelete->ioWrite(['-CP', $row['sku']]);
                }
            }
            $profileExport->ioClose();
            $profileExport->ioClose();

            // add and update feed products
            $import = $this->rapidFlowHelper->run($this->borntechiesHelper->getProductImportProfile($storeCode));

            if ($import->getCurrentActivity() != __('Done')) {
                throw new LocalizedException(__('Error during product import'));
            }

            $this->addMessage(__('Import products to update'));
            $this->addMessage(__('Rows Found %1', $import->getRowsFound()));
            $this->addMessage(__('Rows Processed %1', $import->getRowsProcessed()));
            $this->addMessage(__('Rows Successful %1', $import->getRowsSuccess()));
            $this->addMessage(__('Rows Depends %1', $import->getRowsDepends()));
            $this->addMessage(__('Rows Not Changed %1', $import->getRowsNochange()));
            $this->addMessage(__('Rows Empty/Comment %1', $import->getRowsEmpty()));
            $this->addMessage(__('Rows With Errors %1', $import->getRowsErrors()));
            $this->addMessage(__('Total Error %1', $import->getNumErrors()));
            $this->addMessage(__('Total Warnings %1', $import->getNumWarnings()));

            if (count($productsToDelete)) {
                $this->addMessage(__('Deleting of products that are not in the file started'));
                // update missing products status
                $delete = $this->rapidFlowHelper->run($this->borntechiesHelper->getProductDeleteProfile());
                if ($delete->getCurrentActivity() != __('Done')) {
                    throw new LocalizedException(__('Error during product deleting'));
                }
                $this->addMessage(__('Deleted products: %1', implode(', ', $productsToDelete)));
            } else {
                $this->addMessage(__('Products to delete not found'));
            }
        } else {
            $import = $this->rapidFlowHelper->run($this->borntechiesHelper->getProductImportProfile($storeCode));
            if ($import->getCurrentActivity() != __('Done')) {
                throw new LocalizedException(__('Error during product import'));
            }
        }
    }
}
