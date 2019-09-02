<?php
namespace Borntechies\LicensePlate\Block\Adminhtml\Model\Edit\Button;

use Magento\Backend\Block\Widget\Context;
use Borntechies\LicensePlate\Api\ModelRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Generic
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Generic implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var Context
     */
    protected $context;

    /**
     * @var ModelRepositoryInterface
     */
    protected $modelRepository;

    /**
     * Generic constructor.
     *
     * @param Context                  $context
     * @param ModelRepositoryInterface $modelRepository
     */
    public function __construct(
        Context $context,
        ModelRepositoryInterface $modelRepository
    ) {
        $this->context = $context;
        $this->modelRepository = $modelRepository;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    /**
     * Get model
     *
     * @return int
     */
    public function getModelId()
    {
        try {
            return $this->modelRepository->get(
                $this->context->getRequest()->getParam('id')
            )->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [];
    }
}
