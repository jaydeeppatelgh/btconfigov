<?php
namespace Borntechies\LicensePlate\Helper;

use Borntechies\LicensePlate\Api\Data\ModelProductInterface;
use Borntechies\LicensePlate\Api\Data\ModelRegistrationInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Helper;
use Borntechies\LicensePlate\Api\Data\ModelInterface;

/**
 * Class Validation
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Validation extends Helper\AbstractHelper
{
    /**
     * Model required files
     *
     * @var array
     */
    static $requiredModelFields = [
        ModelInterface::HMDNR,
        ModelInterface::MAKE,
        ModelInterface::MODEL,
        ModelInterface::FUEL,
        ModelInterface::MODEL,
    ];
    /**
     * Required product import fields
     *
     * @var array
     */
    static $requiredProductFields = [
        ProductInterface::SKU,
        ModelInterface::HMDNR
    ];
    /**
     * Required registration fields
     *
     * @var array
     */
    static $requiredRegistrationFields = [
        ModelInterface::HMDNR,
        ModelRegistrationInterface::REGISTRATION
    ];

    /**
     * Check if entered model data is valid
     *
     * @param array $data
     *
     * @return array|bool
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     */
    static public function validateModel($data)
    {
        $errors = [];
        foreach (self::$requiredModelFields as $field) {
            if (!\Zend_Validate::is($data[$field], 'NotEmpty')) {
                $errors[] = __('Field %1 is empty', $field);
            }
        }
        if (!\Zend_Validate::is($data[ModelInterface::FUEL], 'StringLength', ['max' => 1])) {
            $errors[] = __('Fuel field is not valid');
        }

        if (!\Zend_Validate::is($data[ModelInterface::HMDNR], 'Digits')) {
            $errors[] = __('HMDNR field must contain only digits');
        }

        if (count($errors)) {
            return $errors;
        }
        return true;
    }

    /**
     * Check if entered product data is valid
     *
     * @param array $data
     *
     * @return array|bool
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     */
    static public function validateProducts($data)
    {
        $errors = [];

        foreach (self::$requiredProductFields as $field) {
            if (!\Zend_Validate::is($data[$field], 'NotEmpty')) {
                $errors[] = __('Field %1 is empty', $field);
            }
        }

        if (!\Zend_Validate::is($data[ModelInterface::HMDNR], 'Digits')) {
            $errors[] = __('HMDNR field is not valid');
        }

        if (count($errors)) {
            return $errors;
        }
        return true;
    }

    /**
     * Check if entered registration data is valid
     *
     * @param array $data
     *
     * @return array|bool
     * @throws \Exception
     * @throws \Zend_Validate_Exception
     */
    static public function validateRegistrations($data)
    {
        $errors = [];

        foreach (self::$requiredRegistrationFields as $field) {
            if (!\Zend_Validate::is($data[$field], 'NotEmpty')) {
                $errors[] = __('Field %1 is empty', $field);
            }
        }

        if (!\Zend_Validate::is($data[ModelInterface::HMDNR], 'Digits')) {
            $errors[] = __('HMDNR field is not valid');
        }

        if (count($errors)) {
            return $errors;
        }
        return true;
    }
}