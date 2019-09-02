<?php
namespace Borntechies\LicensePlate\Controller\Result;

use Borntechies\LicensePlate\Api\Data\ModelInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Borntechies\LicensePlate\Api\Data\ModelInterfaceFactory;

/**
 * Class Update
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Update extends \Magento\Framework\App\Action\Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Borntechies\LicensePlate\Model\Model
     */
    protected $model;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param ModelInterfaceFactory $model
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        ModelInterfaceFactory $model
    ) {
        parent::__construct($context);
        
        $this->resultJsonFactory = $jsonFactory;
        $this->model = $model->create();
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $response= [];
        $errors = [];
        if ($data) {
            if (isset($data['construction_period']) && $data['construction_period']) {
                if (!isset($data['model']) || !isset($data['manufacturer']) || !isset($data['generation'])) {
                    $errors = __('Model or manufacturer is not selected.');
                } else {
                    $result = $this->model->getResource()->getMotors($data['manufacturer'], $data['model'], $data['generation'], $data['construction_period']);
                    $options = [];
                    foreach ($result as $key => $motor) {
                        $options[$key] = [
                            'model_id' => $motor[ModelInterface::MODEL_ID],
                            'period' => $motor[ModelInterface::FUEL].'-'.$motor[ModelInterface::MOTOR_CODE]
                        ];
                    }
                    $response = [
                        'field' => 'motor',
                        'options' =>  $options,
                        'caption' => __('Select Motor')
                    ];
                }                
            } elseif (isset($data['generation']) && $data['generation']) {
                if (!isset($data['model']) || !isset($data['manufacturer']) ) {
                    $errors = __('Model or manufacturer is not selected.');
                } else {
                    $result = $this->model->getResource()->getConstructionPeriod($data['manufacturer'], $data['model'], $data['generation']);

                    $response = [
                        'field' => 'construction_period',
                        'options' =>  array_combine($result, $result),
                        'caption' => __('Select Construction Period')
                    ];
                }                
            } elseif (isset($data['model']) && $data['model']) {
                if (!isset($data['manufacturer'])) {
                    $errors = __('Model is not selected.');
                } else {
                    $result = $this->model->getResource()->getGenerations($data['manufacturer'], $data['model']);

                    $response = [
                        'field' => 'generation',
                        'options' => array_combine($result, $result),
                        'caption' => __('Select Generation')
                    ];
                }                
            } elseif (isset($data['manufacturer']) && $data['manufacturer']) {
                $result = $this->model->getResource()->getModels($data['manufacturer']);
                $response = [
                    'field' => 'model',
                    'options' => array_combine($result, $result),
                    'caption' => __('Select Model')
                ];
            } else {
                $errors[] = __('Nothing is selected.');
            }
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        if (!$errors) {
            return $resultJson->setData($response);
        }
        return $resultJson->setData(['errors' => $errors]);
    }
}