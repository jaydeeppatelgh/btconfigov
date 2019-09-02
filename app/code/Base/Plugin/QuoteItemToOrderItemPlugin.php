<?php
namespace Borntechies\Base\Plugin;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Cms\Controller\Index\Index;
use Magento\Framework\Controller\Result\Forward;

/**
 * Class QuoteItemToOrderItemPlugin
 *
 * @package Borntechies\Base\Plugin
 */
class QuoteItemToOrderItemPlugin
{
    public function aroundConvert(\Magento\Quote\Model\Quote\Item\ToOrderItem $subject, callable $proceed, $quoteItem, $data)
    {

        // get order item
        $orderItem = $proceed($quoteItem, $data);


        if(!$orderItem->getParentItemId() && $quoteItem->getProduct()->getDealnr()){
            $orderItem->setData('dealer_nr', $quoteItem->getProduct()->getDealnr());
        }

        return $orderItem;
    }
}