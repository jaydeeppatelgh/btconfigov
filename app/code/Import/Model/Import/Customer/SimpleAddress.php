<?php
namespace Borntechies\Import\Model\Import\Customer;

use Magento\CustomerImportExport\Model\Import\Address;
use Magento\CustomerImportExport\Model\Import\CustomerComposite;
use Magento\ImportExport\Model\Import\AbstractEntity;

/**
 * Class SimpleAddress
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class SimpleAddress extends CustomerComposite
{
    /**
     * Prepare validated row data for saving to db
     *
     * @param array $rowData
     *
     * @return array
     */
    protected function _prepareRowForDb(array $rowData)
    {
        $rowData['_scope'] = $this->_getRowScope($rowData);
        $rowData[Address::COLUMN_WEBSITE] =
            $this->_currentWebsiteCode;
        $rowData[Address::COLUMN_EMAIL] = $this->_currentEmail;

        return AbstractEntity::_prepareRowForDb($rowData);
    }

    /**
     * Validate address row
     *
     * @param array $rowData
     * @param int $rowNumber
     *
     * @return bool
     */
    protected function _validateAddressRow(array $rowData, $rowNumber)
    {
        if ($this->getBehavior() == \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE) {
            return true;
        }

        $rowData = $this->_prepareAddressRowData($rowData);
        if (empty($rowData)) {
            return true;
        } else {
            $rowData[Address::COLUMN_WEBSITE] =
                $this->_currentWebsiteCode;
            $rowData[Address::COLUMN_EMAIL] =
                $this->_currentEmail;
            if (!isset($rowData[Address::COLUMN_ADDRESS_ID])) {
                $rowData[Address::COLUMN_ADDRESS_ID] = null;
            }

            return $this->_addressEntity->validateRow($rowData, $rowNumber);
        }
    }

    /**
     * Is attribute contains particular data (not plain customer attribute)
     *
     * @param string $attributeCode
     *
     * @return bool
     */
    public function isAttributeParticular($attributeCode)
    {
        if (str_replace(self::COLUMN_ADDRESS_PREFIX, '', $attributeCode) == Address::COLUMN_ADDRESS_ID) {
            return true;
        }
        if (in_array(str_replace(self::COLUMN_ADDRESS_PREFIX, '', $attributeCode), $this->_addressAttributes)) {
            return true;
        } else {
            return parent::isAttributeParticular($attributeCode);
        }
    }

    /**
     * Imported entity type code getter
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'customers_simple_address';
    }
}