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

class Buscape_Ebit_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isAvailable($store = null)
    {
        $accountId = Mage::getStoreConfig(Buscape_Ebit_Model_Config::XML_PATH_ACCOUNT, $store);
        return $accountId && Mage::getStoreConfigFlag(Buscape_Ebit_Model_Config::XML_PATH_ACTIVE, $store);
    }
    
    public function isSealAvailable($store = null)
    {
        $accountId = Mage::getStoreConfig(Buscape_Ebit_Model_Config::XML_PATH_STORE_NAME, $store);
        return $accountId && Mage::getStoreConfigFlag(Buscape_Ebit_Model_Config::XML_PATH_ACTIVE, $store);
    }
}