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
class Indexa_Mc_Model_Resource_Log_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        $this->_init('indexa_mc/log', 'id');
    }

    /**
     * Filter by Order
     *
     * @param integer $order
     * @return Indexa_Mc_Model_Resource_Log_Collection
     */
    public function addOrderFilter($order) {
        $this->addFieldToFilter('main_table.order_id', $order);
        return $this;
    }

    /**
     * Filter by Robot
     *
     * @param string $robot
     * @return Indexa_Mc_Model_Resource_Log_Collection
     */
    public function addRobotFilter($robot) {
        $this->addFieldToFilter('main_table.robot', $robot);
        return $this;
    }

}

