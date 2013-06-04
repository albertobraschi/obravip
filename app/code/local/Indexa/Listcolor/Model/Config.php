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
 * Product collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Indexa_Listcolor_Model_Config extends Mage_Core_Model_Abstract
{
	const LOAD_BY = 'attribute_code';
	
	const GLUE = 'supplier_reference';
	const COLOR = 'color';
	const OTHER_COLORS = 'other_colors';
	
	/* Invalidar índice para indicar que reindex deve ser refeito
	 * Mas após alguns testes percebeu-se que esse disparo já é automático
	 * Ao se salvar um atributo da EAV
	 **/ 
//	const INDEX_CODE_TO_INVALID = 'catalog_product_attribute';

	public function getOtherColors()
	{
		return self::OTHER_COLORS;
	}
	
	public function getGlueId()
	{
		return self::GLUE;
	}
	
	public function getAttributesList()
	{
		return array(self::GLUE, self::COLOR, self::OTHER_COLORS);
	}
}