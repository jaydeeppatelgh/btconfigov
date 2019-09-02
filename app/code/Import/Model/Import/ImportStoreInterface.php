<?php
namespace Borntechies\Import\Model\Import;
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface ImportStoreInterface
 *
 * @author      Anil <lyudmila@hoofdfabriek.nl>
 */
interface ImportStoreInterface
{
    /**
     * Import data
     *
     * @param string $storeCode
     * @return void
     * @throws LocalizedException
     */
    public function import($storeCode);
}
