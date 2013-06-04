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
class Indexa_Mc_Model_Resource_Payment_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        $this->_init('indexa_mc/payment', 'id');
    }

    /**
     * Filter by status
     *
     * @param string $status
     * @return Indexa_Mc_Model_Resource_Payment_Collection
     */
    public function addStatusFilter($status) {
        $this->addFieldToFilter('main_table.status', $status);
        return $this;
    }

    /**
     * Filter by abandoned status
     *
     * @param integer $abandoned
     * @return Indexa_Mc_Model_Resource_Payment_Collection
     */
    public function addAbandonedFilter($abandoned) {
        $this->addFieldToFilter('main_table.abandoned', $abandoned);
        return $this;
    }
    /**
     * Filter by type 
     *
     * @param integer $type
     * @return Indexa_Mc_Model_Resource_Payment_Collection
     */
    public function addTypeFilter($type) {
        $this->addFieldToFilter('main_table.type', $type);
        return $this;
    }
    /**
     * Filter Time  
     *
     * @param integer $time
     * @return Indexa_Mc_Model_Resource_Payment_Collection
     */
    public function addTimeFilter( $time ) {
        if( !$time || $time < 0 ){
            $time = 1;
        }
        
        $this->addFieldToFilter('main_table.updated_at',  array('to'=>date("Y-m-d H:i:s", strtotime("-{$time} hours") ) ) );
        return $this;
    }

}

