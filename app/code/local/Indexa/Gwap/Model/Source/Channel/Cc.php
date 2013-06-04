<?php

/**
 * Indexa - Gwap Payment Module
 *
 * @title      Magento -> Custom Payment Module for Gwap
 * @category   Payment Gateway
 * @package    Indexa_Gwap
 * @author     Indexa Development Team <desenvolvimento@indexainternet.com.br>
 * @copyright  Copyright (c) 2011 Indexa - http://www.indexainternet.com.br
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Indexa_Gwap_Model_Source_Channel_Cc {

    public function toOptionArray() {
        return array(
            array('value' => 'cielo', 'label' => Mage::helper('gwap')->__('Cielo')),
            array('value' => 'rcard', 'label' => Mage::helper('gwap')->__('Rede Card'))
        );
    }

}