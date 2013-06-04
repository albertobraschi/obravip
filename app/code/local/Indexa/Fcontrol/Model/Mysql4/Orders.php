<?php

/**
 * Indexa Module for Fcontrol
 *
 * @title      Magento -> Custom Module for Fcontrol
 * @category   Fraud Control Gateway
 * @package    Indexa_Fcontrol
 * @author     Indexa Team -> desenvolvimento [at] indexainternet [dot] com [dot] br
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright  Copyright (c) 2011 Indexa - http://www.indexainternet.com.br
 */
class Indexa_Fcontrol_Model_Mysql4_Orders extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('fcontrol/orders', 'id');
    }

}