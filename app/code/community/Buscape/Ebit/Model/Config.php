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

class Buscape_Ebit_Model_Config extends Varien_Object
{
    const XML_PATH              = 'sales/ebit/';
    
    const XML_PATH_ACTIVE       = 'sales/ebit/active';
    
    const XML_PATH_ACCOUNT      = 'sales/ebit/account';
    
    const XML_PATH_STORE_NAME   = 'sales/ebit/store';
    
    protected $_urlAction = "https://www.ebitempresa.com.br/bitrate/pesquisa1.asp";
    
    protected $_srcAction = "https://www.ebitempresa.com.br/bitrate/banners/b%s.gif";
    
    protected $_config = array();
    
    public function getConfigData($key, $storeId = null)
    {
        if (!isset($this->_config[$key][$storeId])) {
            $value = Mage::getStoreConfig(self::XML_PATH . $key, $storeId);
            $this->_config[$key][$storeId] = $value;
        }
        return $this->_config[$key][$storeId];
    }
    
    public function getAccount($store = null)
    {
        if (!$this->hasData('ebit_account')) {
            $this->setData('ebit_account', $this->getConfigData('account', $store));
        }
        
        return $this->getData('ebit_account');
    }
    
    public function getStore($store = null)
    {
        if (!$this->hasData('ebit_store')) {
            $this->setData('ebit_store', $this->getConfigData('store', $store));
        }
        
        return $this->getData('ebit_store');
    }
    
    public function getUrlAction()
    {
        if (!$this->hasData('ebit_url_action')) {
            $this->setData('ebit_url_action', $this->_urlAction);
        }
        
        return $this->getData('ebit_url_action');
    }
    
    public function getSrc()
    {
        if (!$this->hasData('ebit_src')) {
            $this->setData('ebit_src', sprintf($this->_srcAction, '1' . $this->getAccount() . '5'));
        }
        
        return $this->getData('ebit_src');
    }
}