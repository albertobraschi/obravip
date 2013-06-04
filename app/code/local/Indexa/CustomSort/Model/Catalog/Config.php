<?php
 
class Indexa_CustomSort_Model_Catalog_Config extends Mage_Catalog_Model_Config
{
    public function getAttributeUsedForSortByArray()
    {
    	$options = parent::getAttributeUsedForSortByArray();
    	unset($options['position']);
        $options['created_at'] = Mage::helper('catalog')->__('Last Added');
        return $options ;
    }
}