<?php

/**
 * Indexa + Conversão Module
 *
 * @title      Magento -> + Conversão Module
 * @category   Payment Gateway
 * @package    Indexa_Mc
 * @author     Indexa Team -> desenvolvimento [at] indexainternet [dot] com [dot] br
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright  Copyright (c) 2011 Indexa - http://www.indexainternet.com.br
 */
class Indexa_Mc_Model_Observer /* extends Mage_Core_Model_Abstract */ {
    
    const SALES_ORDER_VIEW_INFO_BLOCK = 'Mage_Adminhtml_Block_Sales_Order_View_Info';

    public function salesOrderMcBlock($observer) {
        if (self::SALES_ORDER_VIEW_INFO_BLOCK == get_class($observer->getBlock())) {
            $observer->getTransport()->setHtml($observer->getTransport()->getHtml()
                    . $observer->getBlock()->getLayout()->createBlock('indexa_mc/adminhtml_log')->toHtml()
            );
        } else {
            $observer->getTransport()->setHtml($observer->getTransport()->getHtml());
        }
    }
}
