<?php

class Indexa_Listcolor_Block_Product_View_Others extends Mage_Core_Block_Template
{
	public function getJsonProductsViews()
	{
		$register = Mage::registry('indexa_current_colors');
		$original_product = Mage::registry('product');
		$result = array();
		
		if (is_array($register))
		{
			$product_loader = Mage::getModel('catalog/product');
			
			foreach ($register as $product)
			{
				if (array_key_exists("entity_id", $product))
				{
					$product_loader->reset()->clearInstance()->load($product["entity_id"]);
					
					Mage::unregister('product');
					Mage::register('product', $product_loader);
					
					$layout = Mage::app()->getLayout();
					$layout->getUpdate()
					    ->addHandle('default')
					    ->addHandle('catalog_product_view_remove_list')
					    ->load();
					$layout->generateXml()
       					->generateBlocks();
					
					$block = $layout->getBlock($this->getBaseBlockId());
					$media = $layout->getBlock('product.info.media');
					if (is_object($block))
					{
						$block->setProductId($product["entity_id"]);

						if (is_object($media))
						{
							$media->setProductId($product["entity_id"]);
						}
						
						$html = $block->toHtml();
					}
					
					$result[$product["entity_id"]] = $html;
				}
			}

			Mage::unregister('product');
			Mage::register('product', $original_product);
			return Mage::helper('core')->jsonEncode($result);
		}
	}
}