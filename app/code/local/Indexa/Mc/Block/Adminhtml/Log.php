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
class Indexa_Mc_Block_Adminhtml_Log extends Mage_Payment_Block_Form {

    public function _construct() {
        parent::_construct();
        $this->setTemplate('indexa_mc/log.phtml');
    }

    public function getOrder() {
        return Mage::registry('current_order');
    }

    public function getLog() {
        
        if( $this->getOrder() )
        return Mage::getModel('indexa_mc/log')->getCollection()->addOrderFilter($this->getOrder()->getId())->setOrder('id', 'DESC');
    }

}