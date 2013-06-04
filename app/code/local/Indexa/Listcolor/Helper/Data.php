<?php

class Indexa_Listcolor_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getColors ($_product)
	{
		$col = Mage::getSingleton('catalog/product')->getCollection()
		->addAttributeToFilter('ref_fornecedor',$_product->getRefFornecedor())
		->addAttributeToFilter('type_id',Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE);

		return $col;
	}
	
	public function getColorsSql ($_customer, $_product, $website_id, $store_id)
	{
		$return = array();
		
		if ($_product instanceof Mage_Catalog_Model_Product
			&& $_product->getId() > 0)
		{
			$data = Mage::getSingleton('core/resource')->getConnection('core_read');
			
			// preparing data
			$glue_code = Mage::getModel('indexa_listcolor/config')->getGlueId();
			$glue_id = $_product->getData($glue_code);

			$customer_group = $_customer->getGroupId();
			$product_type = $_product->getTypeId();
			$product_id = $_product->getId();
			
			// raw query
			$query = "
SELECT
  e.entity_id
FROM catalog_product_flat_{$store_id} AS e
  INNER JOIN catalog_product_index_price AS price_index
    ON price_index.entity_id = e.entity_id
      AND price_index.website_id = '{$website_id}'
      AND price_index.customer_group_id = {$customer_group}
WHERE (e.{$glue_code} = '{$glue_id}')
    AND (e.type_id = '{$product_type}')
";
			
//     AND (e.entity_id != '{$product_id}')
			
			$return = $data->fetchAll($query);
		}
		return $return;
	}
	
	public function normalizaColorName($string)
	{
		//return Mage::helper('core')->removeAccents($string);
		return $this->_normalizaColorName(Mage::helper('core')->removeAccents($string));
	}
	
	private function _normalizaColorName($string)
	{
		$from = array('À','Á','Ã','Â','É','Ê','Í','Ó','Õ','Ô','Ú','Ü','Ç','à','á','ã','â','é','ê','í','ó','õ','ô','ú','ü','ç','-','/',' ');
	    $to   = array('A','A','A','A','E','E','I','O','O','O','U','U','C','a','a','a','a','e','e','i','o','o','o','u','u','c','_','_','_');
	    return strtolower(str_replace($from, $to, $string));
	}
}
