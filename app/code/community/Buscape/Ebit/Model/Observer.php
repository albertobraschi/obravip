<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to suporte.developer@buscape-inc.com so we can send you a copy immediately.
 *
 * @category   Buscape
 * @package    Buscape_Ebit
 * @copyright  Copyright (c) 2010 Buscapé Company (http://www.buscapecompany.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Buscape_Ebit_Model_Observer
{
    
    const BLOCK_CHECKOUT_SUCCESS = 'Buscape_Ebit_Block_Checkout_Success';
    
    protected function _getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }
    
    private function getConfig()
    {
        return Mage::getModel('ebit/config');
    }
    
    public function setEbitSuccessPageView(Varien_Event_Observer $observer)
    {
        
        $lastOrderId = $this->_getOnepage()->getCheckout()->getLastOrderId();
        
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->createBlock(
            self::BLOCK_CHECKOUT_SUCCESS,
            'ebit_checkout_success',
            array(
                'as' => 'ebit_checkout_success'
            )
        );
        
        $content = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('content');
            
        if($content) {
            
            $block->setOrderIds($lastOrderId);
            
            $block->setUrlAction($this->getConfig()->getUrlAction());
            
            $block->setSrc($this->getConfig()->getSrc());
            
            $block->setAccount($this->getConfig()->getAccount());
            
            //$content->insert($block); before
            
            $content->append($block); // after
        }
    }
}