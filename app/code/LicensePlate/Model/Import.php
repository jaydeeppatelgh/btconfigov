<?php
namespace Borntechies\LicensePlate\Model;

use Borntechies\LicensePlate\Helper\Config as ConfigHelper;
use Borntechies\LicensePlate\Model\Import\AbstractImport;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Import
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Import
{
    /**
     * Archive folder
     */
    const ARCHIVE_FOLDER_NAME = 'archive';

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @var array
     */
    protected $sourcesToUpdate = [];

    /**
     * @var array
     */
    protected $error = [];

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $varDirectory;

    /**
     * @param Import\Models $model
     * @param ConfigHelper $configHelper
     * @param Import\Products $products
     * @param Import\Registrations $registrations
     */
    public function __construct(
        Import\Models $model,
        ConfigHelper $configHelper,
        Import\Products $products,
        Import\Registrations $registrations,
        Filesystem $filesystem
    ) {
        $this->configHelper = $configHelper;
        $this->varDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->sourcesToUpdate = [
            $model,
            $products,
            $registrations
        ];
    }

    /**
     * Run import
     *
     * @return void
     * @throws LocalizedException
     */
    public function import()
    {
        //prepare all updates
        foreach ($this->sourcesToUpdate as $source) {
            if ($this->fileExists($source)) {
                /**@var AbstractImport $source*/
                $source->createTmp();
                $source->importTmpData($this->getImportFilename($source));
                $this->error = array_merge($this->error, $source->getErrors());
            }

        }

        if ($this->error) {
            throw new LocalizedException(__('Something went wrong during import. Please check logs for the details.'));
        }

        //update data
        foreach ($this->sourcesToUpdate as $source) {
            if ($this->fileExists($source)) {
                $source->updateSource();
                $this->archive($source);
            } else {
                $this->error[] = __('File %1 for import doesn\'t exist. Skip update.', $this->getImportFilename($source));
            }
        }
    }

    /**
     * @param AbstractImport $source
     *
     * @return string
     */
    protected function  getImportFilename(AbstractImport $source)
    {
        $reflect = new \ReflectionClass($source);
        $className = $reflect->getShortName();
        $method = "getImport{$className}Filename";
        return $this->configHelper->$method();
    }

    /**
     * Check if import file exists
     *
     * @param AbstractImport $source
     *
     * @return bool
     */
    protected function fileExists(AbstractImport $source)
    {
        $filename = $this->getImportFilename($source);
        $path = $this->configHelper->getImportFilepath().DIRECTORY_SEPARATOR;
        $sourceFileRelative = $this->varDirectory->getRelativePath($path.$filename);

        if (!$this->varDirectory->isExist($sourceFileRelative)) {
           return false;
        }

        return true;
    }

    /**
     * Move file to archive folder
     *
     * @param AbstractImport $source
     *
     * @return void
     */
    protected function archive(AbstractImport $source)
    {
        $filename = $this->getImportFilename($source);
        $path = $this->configHelper->getImportFilepath().DIRECTORY_SEPARATOR;
        $sourceFileRelative = $this->varDirectory->getRelativePath($path.self::ARCHIVE_FOLDER_NAME);

        if (!$this->varDirectory->isExist($sourceFileRelative)) {
            $this->varDirectory->create($sourceFileRelative);
        }

        $this->varDirectory->renameFile($path.$filename, $sourceFileRelative.DIRECTORY_SEPARATOR.date('d-m-Y-').$filename);
    }

    /**
     * Get error during import
     *
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Get errors as a string message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return implode(PHP_EOL, $this->getError());
    }
}