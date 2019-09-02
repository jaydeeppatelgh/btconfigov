<?php
namespace Borntechies\LicensePlate\Helper;

use Magento\Framework\App\Helper;

/**
 * Class Config
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Config extends Helper\AbstractHelper
{
    const QUERY_VAR_NAME = 'r';
    const QUERY_LENGTH   = 6;
    const MAX_QUERY_LENGTH = 8;

    const PATH_IMPORT_FILEPATH      = 'queue_manager/licenseplate_import/file_path';
    const PATH_IMPORT_MODELS        = 'queue_manager/licenseplate_import/model_filename';
    const PATH_IMPORT_REGISTRATIONS = 'queue_manager/licenseplate_import/registrations_filename';
    const PATH_IMPORT_PRODUCTS      = 'queue_manager/licenseplate_import/products_filename';

    const PATH_REDIRECT_TO_PAGE     = 'license_plate_settings/general/redirect_on_remove_filter';
    const PATH_REDIRECT_BACK        = 'license_plate_settings/general/redirect_back';

    /**
     * Get directory where import files are located
     *
     * @return string|null
     */
    public function getImportFilepath()
    {
        return $this->scopeConfig->getValue(self::PATH_IMPORT_FILEPATH);
    }

    /**
     * Get import models filename
     *
     * @return string|null
     */
    public function getImportModelsFilename()
    {
        return $this->scopeConfig->getValue(self::PATH_IMPORT_MODELS);
    }

    /**
     * Get import registrations filename
     *
     * @return string|null
     */
    public function getImportRegistrationsFilename()
    {
        return $this->scopeConfig->getValue(self::PATH_IMPORT_REGISTRATIONS);
    }

    /**
     * Get import products filename
     *
     * @return string|null
     */
    public function getImportProductsFilename()
    {
        return $this->scopeConfig->getValue(self::PATH_IMPORT_PRODUCTS);
    }

    /**
     * Get redirect page on filter removal
     *
     * @return mixed
     */
    public function getRedirectPage()
    {
        return $this->scopeConfig->getValue(self::PATH_REDIRECT_TO_PAGE);
    }

    /**
     * Get redirect back to the same page setting on filter removal
     *
     * @return mixed
     */
    public function getRedirectBack()
    {
        return $this->scopeConfig->getValue(self::PATH_REDIRECT_BACK);
    }
}