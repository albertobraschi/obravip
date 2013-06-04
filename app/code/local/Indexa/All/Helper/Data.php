<?php

class Indexa_All_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    public function __construct() {
        $this->validate();        
    }
    
    public function validate()
    {
        $filename = 'license.xml';
        $configFile = Mage::app()->getConfig()->getModuleDir('etc', 'Indexa_All') . DS . $filename;
        $lFile = new Mage_Core_Model_Config_Base();
        if (!$lFile->loadFile($configFile)) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid lincense file "%s".', $filename));
        }

        $keys = $lFile->getNode('keys')->asArray();
        $show_exception = true;
        foreach ($keys as $key) {
            if ($key == md5(str_replace(array('https://', 'http://', 'index.php/'), array('', '', ''), Mage::getBaseUrl()) . $this->getPath() )) {
                $show_exception = false;
            }
        }

        if ($show_exception) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid lincense key. Please, edit "%s" with a valid key.', $configFile));
        }
        return $this;
    }
}
