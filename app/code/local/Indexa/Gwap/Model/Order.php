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
class Indexa_Gwap_Model_Order extends Mage_Core_Model_Abstract {
    
    const STATUS_AUTHORIZED = 'authorized';
    const STATUS_CAPTURED = 'captured';
    const STATUS_CAPTUREPAYMENT = 'capture payment';
    const STATUS_CREATED = 'created';
    const STATUS_DENIED = 'denied';
    const STATUS_ERROR = 'error';
    const STATUS_FINISHED = 'finished';
    const STATUS_MAXTRIES = 'max tries';
    const STATUS_PROCESSING = 'processing';
    
    public function _construct() {
        $this->_init('gwap/order');
    }

}