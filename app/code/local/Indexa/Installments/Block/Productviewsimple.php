<?php

class Indexa_Installments_Block_Productviewsimple extends Indexa_Installments_Block_Abstract
{
	public function _construct()
	{
		$this->setTemplate('indexa_installments/productviewsimple.phtml');
	}
	
	/*
	 * 
	 */
	public function getInstallmentHighest()
	{
		if (!$this->getValue())
		{
			Mage::throwException('A value must be set for Installments to render correctly.');
		}
		return $this->getModel()->setValue($this->getValue())->getInstallmentHighest();
	}
}