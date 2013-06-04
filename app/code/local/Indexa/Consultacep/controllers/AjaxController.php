<?php
class Indexa_Consultacep_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function cepAction()
    {
        $collectionResult = Mage::getModel('indexa_consultacep/consultacep')
            ->getCollection();

        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load("consultacep_result_ajax");
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        
        $this->getResponse()->setBody($output);
    }



    /**
     * CLEAN CEP FROM SESSION
     */
    public function cleancepAction() {
        try {
            Mage::getSingleton('core/session')->unsetData('frontend_cep');
            Mage::getSingleton('core/session')->unsetData('frontend_cep_is_valid');
            $this->getResponse()->setBody('sem cep');

        } catch (Exception $e) {
            //
        }
    }
    
    public function checkoutGetCepAction()
    {
   	$postcode = preg_replace("/[^0-9]/","", $this->getRequest()->getParam('cep', false) );
    	
    	
    	$modelConsultaCep = Mage::getModel('indexa_consultacep/consultacep');
    	
    	$result = $modelConsultaCep->getCollection()->getLocationByCEP($postcode)->getData();
		
		$toJsonResult = false;
		
		if(isset($result[0]['uf_sigla']))
		$region_id = Mage::getModel('directory/region')->getCollection()->
							addRegionCodeFilter($result[0]['uf_sigla'])->
							addCountryFilter('BR')->getFirstItem()->getId();
		
		$result[0]['region_id'] = $region_id;
		
		if (is_array($result) && array_key_exists(0, $result))
		{
			$toJsonResult = $result[0];
		} else {
			$this->_getOnepage()->getQuote()->getBillingAddress()->setPostcode(null);			
			$postcode = "";
		}
		
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($toJsonResult) );
    }
    
	private function _getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }
    
}
