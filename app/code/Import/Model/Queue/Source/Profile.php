<?php
namespace Borntechies\Import\Model\Queue\Source;

use Magento\Framework\Option\ArrayInterface;
use Unirgy\RapidFlow\Model\Profile as UrapidFlowProfile;

/**
 * Class Profile
 *
 * @author      Anil <anil.shah@borntechies.com>
 */
class Profile implements ArrayInterface
{
    /**
     * @var UrapidFlowProfile
     */
    protected $profile;

    /**
     * @param UrapidFlowProfile $rapidFlowModelProfile
     */
    public function __construct(
        UrapidFlowProfile $rapidFlowModelProfile
    ) {
        $this->profile = $rapidFlowModelProfile;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $collection = $this->profile->getCollection();
        $options = [];
        foreach ($collection as $item) {
            $options[] = ['value' => $item->getProfileId(), 'label' => $item->getTitle()];
        }

        return $options;
    }
}