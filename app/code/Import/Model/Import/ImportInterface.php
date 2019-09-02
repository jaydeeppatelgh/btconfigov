<?php
namespace Borntechies\Import\Model\Import;
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface ImportInterface
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
interface ImportInterface
{
    /**
     * Import data
     *
     * @return void
     * @throws LocalizedException
     */
    public function import();
}
