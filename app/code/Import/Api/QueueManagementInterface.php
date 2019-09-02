<?php
namespace Borntechies\Import\Api;

use Borntechies\Import\Api\Data\QueueInterface;

/**
 * Interface QueueManagementInterface
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
interface QueueManagementInterface
{
    /**
     * Add Queue Report
     *
     * @param string $type
     * @param null|string $status
     *
     * @return QueueInterface
     */
    public function addReport($type, $status = null);

    /**
     * Update queue report
     *
     * @param string $updateSummary
     * @param null|string $status
     *
     * @return QueueInterface
     */
    public function updateReport($updateSummary, $status = null);

    /**
     * Mark queue report as invalid
     *
     * @param string $updateSummary
     *
     * @return QueueInterface
     */
    public function invalidateReport($updateSummary);

}