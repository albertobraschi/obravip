<?php
/**
 * Indexa - MClearSale Anti-Fraud Module
 *
 * @title      Magento -> Indexa MClearSale module
 * @category   Payment Anti-Fraud
 * @package    Indexa_MClearSale
 * @author     Indexa Development Team -> desenvolvimento [at] indexainternet [dot] com  [dot] br
 * @copyright  Copyright (c) 2009 Indexa - http://www.indexainternet.com.br
 */

class Indexa_MClearSale_Model_MClearSale extends Mage_Core_Model_Abstract
{
	public function getCategoryName($product)
	{
		$category = $product->getData('category');
		
		if (is_null($category) && $product->getCategoryIds())
		{
			$categoryId = $product->getCategoryIds();
			if (is_array($categoryId)) $categoryId = $categoryId[0];
			$category = Mage::getModel('catalog/category')->load($categoryId);
		}

		if (is_object($category))
			return $category->getName();
		else
			return false;
	}
}