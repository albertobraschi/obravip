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
class Indexa_Gwap_Model_Mysql4_Order_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        $this->_init('gwap/order', 'id');
    }

    /**
     * Filter by status
     *
     * @param string $status
     * @return Indexa_Braspag_Model_Mysql4_Orders_Collection
     */
    public function addStatusFilter($status) {
        $this->addFieldToFilter('main_table.status', $status);
        return $this;
    }
    /**
     * Filter by type
     *
     * @param string $type
     * @return Indexa_Braspag_Model_Mysql4_Orders_Collection
     */
    public function addTypeFilter($type) {
        $this->addFieldToFilter('main_table.type', $type);
        return $this;
    }

    /**
     * Filter by abandoned status
     *
     * @param integer $abandoned
     * @return Indexa_Braspag_Model_Mysql4_Orders_Collection
     */
    public function addAbandonedFilter($abandoned) {
        $this->addFieldToFilter('main_table.abandoned', $abandoned);
        return $this;
    }

    
    /**
     * Filter Time  
     *
     * @param integer $time
     * @return Indexa_Mc_Model_Resource_Payment_Collection
     */
    public function addExpireFilter( $time ) {
       
        $this->addFieldToFilter('main_table.created_at',  array('to'=> date("Y-m-d H:i:s", $time )) );
        return $this;
    }
}

