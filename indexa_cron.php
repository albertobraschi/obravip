<?php
/**
 * @author     <gabriel@indexainternet.com.br>
 */
require_once 'app/Mage.php';
Mage::app();
try {
    Mage::getModel('productalert/observer')->process();
} catch (Exception $e) {
    Mage::log('error-0-start $e=' . $e->getMessage() . ' @' . now(), false, 'product_alert_stock_error.log', true);
}