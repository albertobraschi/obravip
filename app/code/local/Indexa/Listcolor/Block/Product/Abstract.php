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
class Indexa_Listcolor_Block_Product_Abstract extends Mage_Core_Block_Template
{
	const DEFAULT_FORMAT = '.jpg';
	const NO_IMAGE = 'no-image';
	const SKIN_FOLDER = 'indexa/listcolor/images/filter';
	
	private $_productModel;
	private $_helper;

	public function transformRowToProduct($_row)
	{
		$pd = $this->_loadProduct();
		
		if (is_array($_row))
		{
			if (array_key_exists('entity_id',$_row))
			{
				$id = $_row['entity_id'];

				if ($id > 0)
				{
					$pd = $this->_loadProduct($id);
				}
			}
		}
		return $pd;
	}
	
	private function _loadProduct($id = -1)
	{
		if ($this->_productModel == null)
		{
			$this->_productModel = Mage::getModel('catalog/product');
		}
		$this->_productModel->clearInstance();
		
		if ($id > 0)
		{
			$this->_productModel->load($id);
		}
		
		return $this->_productModel;
	}

	private function _getHelper()
	{
		if ($this->_helper == null)
		{
			$this->_helper = Mage::helper('indexa_listcolor');
		}
		return $this->_helper;
	}
	
	/*
	 * Método aqui explicitado para
	 * ser sobrescrito em algum caso 
	 */
	public function getDefaultFormat()
	{
		return self::DEFAULT_FORMAT;
	}
	
	/*
	 * Método aqui explicitado para
	 * ser sobrescrito em algum caso 
	 */
	public function getNoImage()
	{
		return self::NO_IMAGE;
	}
	
	/*
	 * Método aqui explicitado para
	 * ser sobrescrito em algum caso 
	 */
	public function getSkinFolder()
	{
		return self::SKIN_FOLDER;
	}
	
	public function getColorString($_product)
	{
		return $_product->getResource()->getAttribute('color')->getFrontend()->getValue($_product);
	}
	
	public function getColorUrl($_product)
	{
		$_color = $this->getColorString($_product);
		
		if (strlen($_color) > 0)
		{
			return $this->getSkinUrl(
				$this->getSkinFolder()
				. DS
				. $this->_getHelper()->normalizaColorName($_color)
				. $this->getDefaultFormat()
			);
		}
		else
		{
			return $this->getNoImageColorUrl ();
		}
	}
	
	public function getNoImageColorUrl ()
	{
		return $this->getSkinUrl(
			$this->getSkinFolder()
			. DS . $this->getNoImage()
			. $this->getDefaultFormat()
		);
	}
	
	public function getColors($_product)
	{
		// @TODO Descobrir porque a collection não funciona na tela do produto
		// return Mage::helper('indexa_listcolor')->getColors($_product);
		
		$_customer = Mage::getSingleton('customer/session')->getCustomer();
		$website_id = Mage::app()->getStore()->getWebsiteId();
		$store_id = Mage::app()->getStore()->getId();
		
		$array_colors = Mage::helper('indexa_listcolor')
					->getColorsSql($_customer, $_product, $website_id, $store_id);
		
		Mage::unregister('indexa_current_colors');
		Mage::register('indexa_current_colors', $array_colors);
		
		Mage::unregister('indexa_current_id');
		Mage::register('indexa_current_id', $_product->getId());
		
		$array_others = $array_colors;
		foreach ($array_colors as $key => $color)
		{
			if ($color['entity_id'] == $_product->getId())
			{
				unset($array_others[$key]);
			}
		}
		
		Mage::unregister('indexa_current_other_colors');
		Mage::register('indexa_current_other_colors', $array_others);
		
		return Mage::registry('indexa_current_colors');
	}
}
