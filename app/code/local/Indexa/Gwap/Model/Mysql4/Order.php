<?php

/**
 * Indexa - Braspag Payment Module
 *
 * @title      Magento -> Custom Payment Module for Braspag (Brazil)
 * @category   Payment Gateway
 * @package    Indexa_Braspag
 * @author     Indexa Development Team -> desenvolvimento [at] indexainternet [dot] com  [dot] br
 * @copyright  Copyright (c) 2011 Indexa - http://www.indexainternet.com.br
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Indexa_Gwap_Model_Mysql4_Order extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('gwap/order', 'id');
    }

}