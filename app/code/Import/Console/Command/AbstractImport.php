<?php
namespace Borntechies\Import\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Borntechies\Import\Helper\Data as BorntechiesHelper;
use Borntechies\Import\Api\QueueManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class AbstractImport
 *
 *  @author      Anil <anil.shah@borntechies.com>
 */
abstract class AbstractImport extends Command
{
    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @var QueueManagementInterface
     */
    protected $queueManagement;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var BorntechiesHelper
     */
    protected $borntechiesHelper;

    /**
     * @var ObjectManagerInterface
     */
    protected $manager;

    /**
     * @param AppState $appState
     * @param ObjectManagerInterface $manager
     * @param QueueManagementInterface $queueManagement
     * @param ScopeConfigInterface $scopeConfig
     * @param BorntechiesHelper $borntechiesHelper
     */
    public function __construct(
        AppState $appState,
        ObjectManagerInterface $manager,
        QueueManagementInterface $queueManagement,
        ScopeConfigInterface $scopeConfig,
        BorntechiesHelper $borntechiesHelper
    ) {
        parent::__construct();

        $this->appState = $appState;
        $this->queueManagement = $queueManagement;
        $this->scopeConfig = $scopeConfig;
        $this->borntechiesHelper = $borntechiesHelper;
        $this->manager = $manager;
    }
}