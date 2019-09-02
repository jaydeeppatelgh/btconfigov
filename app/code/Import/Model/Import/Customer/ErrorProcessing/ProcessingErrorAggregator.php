<?php
namespace Borntechies\Import\Model\Import\Customer\ErrorProcessing;

use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregator as MageProcessingErrorAggregator;

/**
 * Class ProcessingErrorAggregator
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class ProcessingErrorAggregator extends MageProcessingErrorAggregator
{
    /**
     * Overwrite to skip rows on invalid row
     *
     * @param string $errorCode
     * @param string $errorLevel
     * @param int|null $rowNumber
     * @param string|null $columnName
     * @param string|null $errorMessage
     * @param string|null $errorDescription
     * @return $this
     */
    public function addError(
        $errorCode,
        $errorLevel = ProcessingError::ERROR_LEVEL_CRITICAL,
        $rowNumber = null,
        $columnName = null,
        $errorMessage = null,
        $errorDescription = null
    ) {
        if ($this->validationStrategy == self::VALIDATION_STRATEGY_STOP_ON_ERROR) {
            return parent::addError($errorCode, $errorLevel, $rowNumber, $columnName, $errorMessage, $errorDescription);
        }

        if ($this->isErrorAlreadyAdded($rowNumber, $errorCode)) {
            return $this;
        }
        $this->processErrorStatistics($errorLevel);
        if ($errorLevel == ProcessingError::ERROR_LEVEL_CRITICAL) {
            $this->addRowToSkip($rowNumber);
        }
        $errorMessage = $this->getErrorMessage($errorCode, $errorMessage, $columnName);

        /** @var ProcessingError $newError */
        $newError = $this->errorFactory->create();
        $newError->init($errorCode, $errorLevel, $rowNumber, $columnName, $errorMessage, $errorDescription);
        $this->items['rows'][$rowNumber][] = $newError;
        $this->items['codes'][$errorCode][] = $newError;
        $this->items['messages'][$errorMessage][] = $newError;
        return $this;
    }


    /**
     * Overwrite to skip error on invalid row
     *
     * @return bool
     */
    public function hasToBeTerminated()
    {
        if ($this->validationStrategy == self::VALIDATION_STRATEGY_STOP_ON_ERROR) {
            return parent::hasToBeTerminated();
        }

        return $this->isErrorLimitExceeded();
    }
    /**
     * @return int
     */
    public function getSkippedRowsCount()
    {
        return count($this->skippedRows);
    }
}