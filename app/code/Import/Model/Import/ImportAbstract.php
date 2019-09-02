<?php
namespace Borntechies\Import\Model\Import;

use Unirgy\RapidFlow\Helper\Data as RapidFlowHelper;
use Borntechies\Import\Helper\Data as BorntechiesHelper;

/**
 * Class Product
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
abstract class ImportAbstract
{
    /**
     * @var RapidFlowHelper
     */
    protected $rapidFlowHelper;

    /**
     * @var BorntechiesHelper
     */
    protected $borntechiesHelper;

    /**
     * @var string
     */
    protected $message = '';

    /**
     * @param RapidFlowHelper $rapidflowHelper
     * @param BorntechiesHelper $borntechiesHelper
     */
    public function __construct(
        RapidFlowHelper $rapidflowHelper,
        BorntechiesHelper $borntechiesHelper
    ) {
        $this->rapidFlowHelper = $rapidflowHelper;
        $this->borntechiesHelper = $borntechiesHelper;
    }

    /**
     * Add new line to message string
     *
     * @param $message
     *
     * @return void
     */
    protected function addMessage($message)
    {
        $this->message .= $message.PHP_EOL;
    }

    /**
     * Get message string
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}