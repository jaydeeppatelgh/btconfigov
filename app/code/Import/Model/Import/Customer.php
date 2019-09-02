<?php
namespace Borntechies\Import\Model\Import;

use Magento\ImportExport\Model\Import as ImportModel;
use Magento\ImportExport\Model\Import\Adapter as ImportAdapter;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\HTTP\Adapter\FileTransferFactory;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem;
use Magento\ImportExport\Helper\Data as ImportExportHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ImportExport\Model\Import\ConfigInterface;
use Magento\ImportExport\Model\Import\Entity\Factory as ImportEntityFactory;
use Magento\ImportExport\Model\ResourceModel\Import\Data as ImportExportResourceModel;
use Magento\ImportExport\Model\Export\Adapter\CsvFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\ImportExport\Model\Source\Import\Behavior\Factory as BehaviorFactory;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\ImportExport\Model\History;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Borntechies\Import\Helper\Data as BorntechiesHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
/**
 * Class Customer
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Customer extends ImportModel implements ImportInterface
{
    const BATCH_STEP = 2000;
    const FIELD_BEHAVIOR = 'behavior';


    /**
     * Collection of existent customers
     *
     * @var CustomerCollection
     */
    protected $customerCollection;

    /**
     * Existing addresses
     *
     * Example Array: [email] => address ID 1,
     *
     * @var array
     */
    protected $addresses = [];

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var BorntechiesHelper
     */
    protected $borntechiesHelper;

    public function __construct(
        LoggerInterface $logger,
        Filesystem $filesystem,
        ImportExportHelper $importExportData,
        ScopeConfigInterface $coreConfig,
        ConfigInterface $importConfig,
        ImportEntityFactory $entityFactory,
        ImportExportResourceModel $importData,
        CsvFactory $csvFactory,
        FileTransferFactory $httpFactory,
        UploaderFactory $uploaderFactory,
        BehaviorFactory $behaviorFactory,
        IndexerRegistry $indexerRegistry,
        History $importHistoryModel,
        DateTime $localeDate,
        CustomerCollectionFactory $customerColFactory,
        BorntechiesHelper $borntechiesHelper,
        StoreManagerInterface $storeManagerInterface,
        array $data = []
    ) {
        parent::__construct($logger, $filesystem, $importExportData, $coreConfig, $importConfig, $entityFactory, $importData, $csvFactory,
            $httpFactory, $uploaderFactory, $behaviorFactory, $indexerRegistry, $importHistoryModel, $localeDate, $data);

        $this->customerCollection = $customerColFactory;
        $this->borntechiesHelper = $borntechiesHelper;
        $this->storeManager = $storeManagerInterface;
    }

    /**
     * Initialize existent addresses data
     *
     * @return $this
     */
    protected function initAddresses()
    {
        /** @var $customer \Magento\Customer\Model\Customer */
        foreach ($this->customerCollection->create() as $customer) {
            $email = $customer->getEmail();
            if (!isset($this->addresses[$customer->getWebsiteId()][$email])) {
                $this->addresses[$customer->getWebsiteId()][$email] = $customer->getData('default_billing');
            }

        }
        return $this;
    }

    /**
     * Import source file structure to DB.
     *
     * @return bool
     * @throws LocalizedException
     */
    public function importSource()
    {
        $this->setData('entity', $this->getDataSourceModel()->getEntityTypeCode());
        $this->setData('behavior', $this->getDataSourceModel()->getBehavior());

        $this->addLogComment(__('Begin import of "%1" with "%2" behavior', $this->getEntity(), $this->getBehavior()));

        $result = $this->processImport();

        if ($result) {
            $this->addLogComment(
                [
                    __(
                        'Checked rows: %1, checked entities: %2, invalid rows: %3, skipped rows: %4 total errors: %5',
                        $this->getProcessedRowsCount(),
                        $this->getProcessedEntitiesCount(),
                        $this->getErrorAggregator()->getInvalidRowsCount(),
                        $this->getErrorAggregator()->getSkippedRowsCount(),
                        $this->getErrorAggregator()->getErrorsCount()
                    ),
                    __('The import was successful.'),
                ]
            );
        } else {
            throw new LocalizedException(__('Can not import data.'));
        }

        return $result;
    }


    /**
     * Retrieve processed reports entity types
     *
     * @param string|null $entity
     *
     * @return bool
     */
    public function isReportEntityType($entity = null)
    {
        return false;
    }


    /**
     * Import/Export working directory (source files, result files, lock files etc.).
     *
     * @return string
     */
    public function getWorkingDir()
    {
        return $this->_varDirectory->getAbsolutePath($this->borntechiesHelper->getCustomerImportDirectory());
    }

    /**
     * Get customers import file name from the configuration
     *
     * @return string
     * @throws LocalizedException
     */
    public function getFilename()
    {
        $filename = $this->borntechiesHelper->getCustomerImportFilename();
        if (!$filename) {
            throw new LocalizedException(__('Customer filename is not specified.'));
        }

        if (substr($this->getWorkingDir(), -1) == DIRECTORY_SEPARATOR) {
            return $this->getWorkingDir() . $filename;
        } else {
            return $this->getWorkingDir() . DIRECTORY_SEPARATOR . $filename;
        }

    }

    /**
     * Split up uploaded file onto parts.
     *
     * @throws LocalizedException
     * @return array
     */
    public function getParts()
    {
        $parts = [];
        $partsGroup = 'customers';
        $sourceFile = $this->getFilename();
        $sourceFileRelative = $this->_varDirectory->getRelativePath($this->getWorkingDir().$partsGroup);

        if ($this->_varDirectory->isExist($sourceFileRelative)) {
            $this->_varDirectory->delete($sourceFileRelative);
        }
        $this->_varDirectory->create($sourceFileRelative);

        $this->initAddresses();

        $file = @fopen($sourceFile, 'r');
        if ($file) {
            $header = null;
            $i = 0;
            $part = 1;
            $partPath = $this->getWorkingDir(). DIRECTORY_SEPARATOR . $partsGroup .
                DIRECTORY_SEPARATOR . $partsGroup . '-part-' . $part . '.csv';
            $partFile = @fopen($partPath, 'w');
            $parts[] = DirectoryList::VAR_DIR . DIRECTORY_SEPARATOR . $this->_varDirectory->getRelativePath($partPath);
            if ($partFile) {
                while (($row = @fgetcsv($file, 5000, ",")) !== FALSE) {
                    if (!$header) {
                        $header = $row;
                        $header[] = \Magento\CustomerImportExport\Model\Import\CustomerComposite::COLUMN_ADDRESS_PREFIX.
                            \Magento\CustomerImportExport\Model\Import\Address::COLUMN_ADDRESS_ID;
                        fputcsv($partFile, $header);
                        continue;
                    }

                    $emailKey = array_search(\Magento\CustomerImportExport\Model\Import\Customer::COLUMN_EMAIL, $header);
                    $websiteIdKey = array_search('website_id', $header);
                    $row[] = isset($this->addresses[$row[$websiteIdKey]][$row[$emailKey]]) ? $this->addresses[$row[$websiteIdKey]][$row[$emailKey]] : '';
                    if ($i < self::BATCH_STEP) {
                        fputcsv($partFile, $row);
                    } else {
                        fclose($partFile);
                        $part++;
                        $i = 0;
                        $partPath = $this->getWorkingDir() .DIRECTORY_SEPARATOR. $partsGroup .
                            DIRECTORY_SEPARATOR . $partsGroup . '-part-' . $part . '.csv';
                        $partFile = @fopen($partPath, 'w');
                        $parts[] = DirectoryList::VAR_DIR . DIRECTORY_SEPARATOR . $this->_varDirectory->getRelativePath($partPath);
                        fputcsv($partFile, $header);
                        fputcsv($partFile, $row);
                    }

                    $i++;
                }
                @fclose($partFile);
            }
            @fclose($file);
        } else {
            $this->_varDirectory->delete($sourceFileRelative);
            throw new LocalizedException(__('Cannot read source file.'));
        }

        return $parts;
    }

    /**
     * Import data
     *
     * @return void
     * @throws LocalizedException
     */
    public function import()
    {
        $data = [
            'behavior' => ImportModel::BEHAVIOR_ADD_UPDATE,
            'entity' => 'customers_simple_address',
            ImportModel::FIELD_NAME_VALIDATION_STRATEGY => 'validation-skip-errors',
            ImportModel::FIELD_NAME_ALLOWED_ERROR_COUNT => 100,
            ImportModel::FIELD_FIELD_SEPARATOR => ',',
            ImportModel::FIELD_FIELD_MULTIPLE_VALUE_SEPARATOR => ','
        ];

        $this->setData($data);
        $parts = $this->getParts();
        $errors = false;

        $this->getErrorAggregator()->initValidationStrategy('validation-skip-errors', 100);

        foreach ($parts as $part) {
            $source = ImportAdapter::findAdapterFor(
                $part,
                $this->_filesystem->getDirectoryWrite(DirectoryList::ROOT),
                $data[self::FIELD_FIELD_SEPARATOR]
            );
           $this->validateSource($source);

            if (!$this->getProcessedRowsCount()) {
                if (!$this->getErrorAggregator()->getErrorsCount()) {
                    $errors = true;
                    $this->addLogComment(__('This file is empty. Please try another one.'));
                } else {
                    foreach ($this->getErrorAggregator()->getAllErrors() as $error) {
                        $errors = true;
                        $this->addLogComment(__( $error->getErrorMessage()));
                    }
                }
            } else {
//                $errorAggregator = $this->getErrorAggregator();
//                if (!$validationResult) {
//                    $errors = true;
//                    $this->addLogComment(__('Data validation is failed. Please fix errors and re-upload the file..'));
//                } else {
                    if ($this->isImportAllowed()) {
                        $this->importSource();
                        $errorAggregator = $this->getErrorAggregator();
                        if ($this->getErrorAggregator()->hasToBeTerminated()) {
                            $errors = true;
                            $this->addLogComment(__('Maximum error count has been reached or system error is occurred!'));
                        } else {
                            $this->invalidateIndex();
                        }
                    } else {
                        $errors = true;
                        $this->addLogComment(__('The file is valid, but we can\'t import it for some reason.'));
                    }
                //}

                $this->addLogComment(__(
                    'Checked rows: %1, checked entities: %2, invalid rows: %3, skipped rows: %4 total errors: %5',
                    $this->getProcessedRowsCount(),
                    $this->getProcessedEntitiesCount(),
                    $this->getErrorAggregator()->getInvalidRowsCount(),
                    $this->getErrorAggregator()->getSkippedRowsCount(),
                    $this->getErrorAggregator()->getErrorsCount()
                ));
                foreach ($this->getErrorAggregator()->getAllErrors() as $error) {
                    $this->addLogComment($error->getErrorMessage());
                }
            }
        }

        if ($errors) {
            throw new LocalizedException(__('Errors occurred during the import. Please check logs for the details. '));
        }
    }

    /**
     * Validates source file and returns validation result.
     *
     * @param \Magento\ImportExport\Model\Import\AbstractSource $source
     * @return bool
     */
    public function validateSource(\Magento\ImportExport\Model\Import\AbstractSource $source)
    {
        $isValid = parent::validateSource($source);

        if ($isValid) {
            $this->addLogComment(__('Begin user duplicated usernames check.'));
            $usernames = $importUsers = [];
            foreach ($source as $rowNum => $item) {
                $usernames[] = $item['username'];
                $importUsers[$item['_website']][$item['username']]['email'] =  $item['email'];
                $importUsers[$item['_website']][$item['username']]['rowNumber'] =  $rowNum;
            }

            $customers = $this->customerCollection->create()->addAttributeToFilter('username', ['in' => $usernames]);

            foreach ($customers as $customer) {
                $website = $this->storeManager->getWebsite($customer->getWebsiteId())->getCode();
                if (isset($importUsers[$website][$customer->getUsername()]) && $importUsers[$website][$customer->getUsername()]['email'] != $customer->getEmail()) {
                    $errorAggregator = $this->getErrorAggregator();
                    $errorAggregator->addError(\Magento\ImportExport\Model\Import\Entity\AbstractEntity::ERROR_CODE_DUPLICATE_UNIQUE_ATTRIBUTE,
                        ProcessingError::ERROR_LEVEL_CRITICAL,
                        $importUsers[$website][$customer->getUsername()]['rowNumber'],
                        'email',
                        null,
                        __('Customer with username %1 is already assigned to the email %2', $customer->getUsername(), $customer->getEmail()));
                    $this->addLogComment(__('Customer with username %1 is already assigned to the email %2', $customer->getUsername(), $customer->getEmail()));
                    $isValid = false;
                }
            }
        }

        return $isValid;
    }
}
