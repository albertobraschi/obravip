<?php 

class Indexa_CustomAttr_Model_Observer
{
	public function customerSaveBefore($observer)
	{
		$customer = $observer->getEvent()->getCustomer();
		$designer_arquiteto = Mage::app()->getRequest()->getPost('designer_arquiteto') ? 1 : 0;
		$customer->setData('designer_arquiteto', $designer_arquiteto);
	}	
}