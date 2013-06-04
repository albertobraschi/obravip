<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Product list
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Indexa_Listcolor_Block_Product_List extends Indexa_Listcolor_Block_Product_Abstract
{
	const DEFAULT_TEMPLATE = 'indexa/listcolor/product/list.phtml';
	
	public function setProduct($_product)
	{
		$this->_productModel = $_product;
		return $this;
	}
	
	public function getProduct()
	{
		if (	$this->_productModel == null ||
				!$this->_productModel instanceof Mage_Catalog_Model_Product ||
				!is_object($this->_productModel) ||
				!method_exists($this->_productModel, 'getId') ||
				!($this->_productModel->getId() > 0)
			)
		{
			Mage::throwException($this->__('Product used to list color is unavailable. Please check design implementation in product listing.'));
		}
		return $this->_productModel;
	}
	
	public function setDefaultTemplate()
	{
		$this->setTemplate(self::DEFAULT_TEMPLATE);
		return $this;
	}
}
