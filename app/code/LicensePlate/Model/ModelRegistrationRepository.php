<?php
namespace Borntechies\LicensePlate\Model;

use Borntechies\LicensePlate\Api\Data;
use Borntechies\LicensePlate\Api\ModelRegistrationRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Borntechies\LicensePlate\Model\ResourceModel\ModelRegistration as ModelRegistrationResource;
use Borntechies\LicensePlate\Model\ResourceModel\ModelRegistration\CollectionFactory as ModelRegistrationCollectionFactory;

/**
 * Class ModelRegistrationRepository
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class ModelRegistrationRepository implements ModelRegistrationRepositoryInterface
{
    /**
     * @var ModelRegistrationResource
     */
    protected $resource;

    /**
     * @var ModelRegistrationCollectionFactory
     */
    protected $registrationCollectionFactory;

    /**
     * @var Data\ModelRegistrationInterfaceFactory
     */
    protected $registrationFactory;

    public function __construct(
        ModelRegistrationCollectionFactory $collectionFactory,
        Data\ModelRegistrationInterfaceFactory $registrationFactory,
        ModelRegistrationResource $resource
    ) {
        $this->registrationCollectionFactory = $collectionFactory;
        $this->registrationFactory = $registrationFactory;
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(Data\ModelInterface $model)
    {
        $collection = $this->registrationCollectionFactory->create()
            ->getModelRegistration($model->getId());

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $registration = $this->registrationFactory->create();
        $this->resource->load($registration, $id);
        if (!$registration->getId()) {
            throw new NoSuchEntityException(__('Registration with id "%1" does not exist.', $id));
        }
        return $registration;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Data\ModelRegistrationInterface $registration)
    {
        try {
            $this->resource->delete($registration);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the registration: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Data\ModelRegistrationInterface $registration)
    {
        try {
            $this->resource->save($registration);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the registration: %1',
                $exception->getMessage()
            ));
        }
        return $registration;
    }

    /**
     * {@inheritdoc}
     */
    public function getByRegistration($query)
    {
        $registration = $this->registrationFactory->create();
        $this->resource->load($registration, $query, Data\ModelRegistrationInterface::REGISTRATION);
        if (!$registration->getId()) {
            throw new NoSuchEntityException(__('Registration with number "%1" does not exist.', $query));
        }
        return $registration;
    }
}