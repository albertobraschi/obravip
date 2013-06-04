<?php

class Indexa_Installments_Block_Productviewtable extends Indexa_Installments_Block_Abstract
{
	public function _construct()
	{
		$this->setTemplate('indexa_installments/productviewtable.phtml');
	}
	
	public function getInstallments()
	{
            	$this->getModel()->setValue($this->getValue());
                return $this->_getInstallments()->returnIterable();
	}
}