<?php

class Indexa_Listcolor_Model_Observer
{
	/*
	 * No momento em que um produto é salvo no admin, juntamos todas as possibilidades de cor deste produto
	 * OUTROS -> PRODUTO ATUAL
	 * 
	 */
	public function gatherInformationFromOthers($observer)
	{
		
//		$product = $observer->getDataObject();
//		$websiteModel = Mage::getModel('core/website');
//		
//		$configModel = Mage::helper('indexa_listcolor');
//		$helper = Mage::helper('indexa_listcolor');
		
		//echo "<PRE>";
//		foreach ($product->getData() as $key => $value)
//		{
//			echo $key . PHP_EOL;
//		}
		
//		$wids = $product->getWebsiteIds();
//		if (is_array($wids))
//		{
//			foreach ($wids as $website_id)
//			{
//				$websiteModel->clearInstance()->load($website_id);
//				$sids = $websiteModel->getStoreIds();
//				
//				if (is_array($sids))
//				{
//					foreach ($sids as $store_id)
//					{
//						if ( $store_id != Mage_Core_Model_App::ADMIN_STORE_ID )
//						{
//							$other_colors = $helper->getColorsSql($product, $website_id, $store_id);
//							
//							var_dump($other_colors);
//						}
//					}
//				}
//			}
//		}
		
//		
//		getColorsSql ($_product);
//		
//		$supplier = $product->getSupplierReference();
//		$other_colors = $product->getOtherColors();
		
//		var_dump($supplier);
//		var_dump($other_colors);
//
//		die();

	}
	
	/*
	 * Após salvar neste produto todas as outras cores, eu atualizo em cada outra cor a existencia desse produto
	 * PRODUTO ATUAL -> OUTROS
	 * 
	 */
	public function syncColorsToOthers($observer)
	{
		
//		$product = $observer->getDataObject();
//		
//		$supplier = $product->getSupplierReference();
//		$other_colors = $product->getOtherColors();
//		
//		
//		echo "<PRE>";
//		foreach ($product->getData() as $key => $value)
//		{
//			echo $key . PHP_EOL;
//		}
//		
//		var_dump($other_colors);
//				
//		die();

	}
}