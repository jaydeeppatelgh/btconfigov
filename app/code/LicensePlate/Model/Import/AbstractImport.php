<?php
namespace Borntechies\LicensePlate\Model\Import;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Borntechies\LicensePlate\Helper\Config as ConfigHelper;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Borntechies\LicensePlate\Api\Data\ModelInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class AbstractImport
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
abstract class AbstractImport
{
    /**
     * Suffix for table to show it is temporary
     */
    const TEMPORARY_TABLE_SUFFIX = '_tmp';

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * Flat columns
     *
     * @var array
     */
    protected $columns = [];

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @var WriteInterface
     */
    protected $varDirectory;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param ResourceConnection $resource
     * @param ConfigHelper $config
     * @param Filesystem $filesystem
     * @param string $table
     */
    public function __construct(
        ResourceConnection $resource,
        ConfigHelper $config,
        Filesystem $filesystem,
        $table = ''
    ) {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->columns = $this->getTmpTableColumns();
        $this->configHelper = $config;
        $this->varDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->table = $table;
    }

    /**
     * Get tmp table columns
     *
     * @return array
     */
    abstract protected function getTmpTableColumns();

    /**
     * Add suffix to table name to show it is temporary
     *
     * @param string $tableName
     *
     * @return string
     */
    protected function addTemporaryTableSuffix($tableName = null)
    {
        $tableName = $this->getTableName($tableName);
        return $tableName . self::TEMPORARY_TABLE_SUFFIX;
    }

    /**
     * Retrieve list of columns for flat structure
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Get table name
     *
     * @param null|string $tableName
     *
     * @return string
     */
    protected function getTableName($tableName = null)
    {
        if (!$tableName) {
            return $this->resource->getTableName($this->table);
        }
        return $this->resource->getTableName($tableName);
    }

    /**
     * Return structure for flat catalog table
     *
     * @param string $tableName
     *
     * @return \Magento\Framework\DB\Ddl\Table
     */
    protected function getTmpTableStructure($tableName)
    {
        $table = $this->connection->newTable(
            $tableName
        )->setComment(
            sprintf("Licenseplate tmp table", $tableName)
        );

        //Adding columns
        foreach ($this->getColumns() as $fieldName => $fieldProp) {
            $table->addColumn(
                $fieldName,
                $fieldProp['type'][0],
                $fieldProp['type'][1],
                [
                    'nullable' => $fieldProp['nullable'],
                    'unsigned' => $fieldProp['unsigned'],
                    'primary'  => isset($fieldProp['primary']) ? $fieldProp['primary'] : false,
                    'identity' => isset($fieldProp['primary']) ? $fieldProp['primary'] : false
                ],
                $fieldProp['comment'] != '' ? $fieldProp['comment'] : ucwords(str_replace('_', ' ', $fieldName))
            );
        }

        return $table;
    }

    /**
     * Create table and add attributes as fields for specified model.
     * This routine assumes that DDL operations are allowed
     *
     * @return $this
     * @throws \Zend_Db_Exception
     */
    protected function createTable()
    {
        $temporaryTable = $this->addTemporaryTableSuffix($this->getTableName());
        $table = $this->getTmpTableStructure($temporaryTable);
        $this->connection->dropTable($temporaryTable);
        $this->connection->createTable($table);

        return $this;
    }

    /**
     * Create tmp tables
     *
     * @return $this
     */
    public function createTmp()
    {
        if ($this->connection->getTransactionLevel() > 0) {
            return $this;
        }
        $this->createTable($this->table);

        return $this;
    }

    /**
     * Check if all required attributes are added to the file
     * in case of error return array of missed fields
     *
     * @param array $headers
     *
     * @return array|bool
     */
    abstract protected function checkCsvFileHeaders($headers);

    /**
     * Add error message
     *
     * @param string $error
     *
     * @return void
     */
    public function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * @param string $filename
     *
     * @return void
     * @throws LocalizedException
     */
    public function importTmpData($filename)
    {
        $file = $this->getFile($filename);
        $resource = $this->varDirectory->openFile($file, 'r');
        $i = 0;
        $data = [];
        $header = null;
        while(false !== ($csvLine = $resource->readCsv())) {
            if (!$header) {
                foreach ($csvLine as $field) {
                    $header[] = strtolower($field);
                }
                $isHeadersValid = $this->checkCsvFileHeaders($header);
                if ($isHeadersValid !== true) {
                    foreach ($isHeadersValid as $error) {
                        $this->addError($error);
                    }
                    $resource->close();
                    throw new LocalizedException(__('CSV file does not contain all required fields.'));
                }
                continue;
            }

            $row = array_combine($header, $csvLine);

            if (!$this->validateCsvRow($row)) {
                continue;
            }

            $data[] = $row;

            if (++$i == 100) {
                $this->connection->insertMultiple(
                    $this->addTemporaryTableSuffix($this->getTableName($this->table)),
                    $data
                );
                $i = 0;
                $data = [];
            }
        }

        if ($data) {
            $this->connection->insertMultiple(
                $this->addTemporaryTableSuffix($this->getTableName($this->table)),
                $data
            );
        }
        $resource->close();
    }

    /**
     * Import working directory (source files, result files, lock files etc.).
     *
     * @return string
     */
    public function getWorkingDir()
    {
        return $this->varDirectory->getAbsolutePath($this->configHelper->getImportFilepath());
    }

    /**
     * Get import file path to read
     *
     * @param string $filename
     *
     * @return string
     * @throws LocalizedException
     */
    public function getFile($filename)
    {
        if (substr($this->getWorkingDir(), -1) == DIRECTORY_SEPARATOR) {
            $file = $this->varDirectory->getRelativePath($this->getWorkingDir() . $filename);
        } else {
            $file = $this->varDirectory->getRelativePath($this->getWorkingDir() . DIRECTORY_SEPARATOR . $filename);
        }

        if (!$this->varDirectory->isExist($file)) {
            throw new LocalizedException(__('Model import file doesn\'t exist'));
        }

        return $file;
    }

    /**
     * Check if all data in CSV file set correctly
     *
     * @param array $row
     *
     * @return bool
     */
    abstract public function validateCsvRow($row);

    /**
     * Update main table from tmp
     *
     * @return void
     * @throws LocalizedException
     */
    abstract public function updateSource();

    /**
     * Get import errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Add model_id to tmp table
     *
     * @return void
     */
    protected function updateTmpModelField()
    {
        $modelTable = 'license_plate_model';
        $subSelect = $this->connection->select()->from(
            $this->getTableName($modelTable),
            [ModelInterface::MODEL_ID, ModelInterface::HMDNR]
        );
        $select = $this->connection->select()->join(
            ['model' => $subSelect],
            'tmp_table.hmdnr = model.hmdnr',
            ['model_id' => 'model.id']
        );
        $updateQuery = $select->crossUpdateFromSelect(['tmp_table' => $this->addTemporaryTableSuffix()]);
        $this->connection->query($updateQuery);
    }
}