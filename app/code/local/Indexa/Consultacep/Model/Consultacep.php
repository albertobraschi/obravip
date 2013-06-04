<?php
class Indexa_Consultacep_Model_Consultacep extends Mage_Core_Model_Abstract
{
	const CEP_FIELD = 'tx_cep';
	const CIDADE_FIELD = 'tx_cidade';
	const BAIRRO_FIELD = 'tx_bairro';
	
	const TIPO_DE_ENVIO_PADRAO = 'package_weight';
	
    protected function _construct()
    {
        $this->_init('indexa_consultacep/consultacep', 'cep');
    }
    
    /*
     * Para o momento que ele checa o cep na área mapa
     */
    public function formaDeliveryType($cep)
    {
    	$collection = $this->getCollection();
    	$data = $collection->getDeliveryTypeText($cep);
    	
    	if (is_object($data))
    	{
    		$data_array = $data->getData();
    		
    		if (is_array($data_array) && array_key_exists(0, $data_array))
    		{
    			return sprintf((string) Mage::getStoreConfig("carriers/matrixrate/frase_envio"), $data_array[0]['bairro'], $data_array[0]['cidade']);
    		}
    	}
    	return false;
    }
    
    /*
     * Para o checkout
     */
    public function formaDeliveryTypeByOriginalCep($cep, $citiesList)
    {
    	$collection = $this->getCollection();
    	$cepFinal = $collection->getCEPinMatrixRates($cep, $citiesList);
    	
    	if (is_object($cepFinal))
    	{
    		$cepFinalData = $cepFinal->getData();
    		
    		if (is_array($cepFinalData) && array_key_exists(0, $cepFinalData))
    		{
    			return sprintf((string) Mage::getStoreConfig("carriers/matrixrate/frase_envio"),
    				$cepFinalData[0]['bairro'],
    				$cepFinalData[0]['cidade']);
    		}
    	}
    	
    	return false;
    }
    
    public function retrieveCityListByWebsite($website = false)
    {
    	if (!$website) $website = Mage::app()->getWebsite()->getId();
    	
    	$citiesObject = $this->getCollection()->getMatrixRatesCities($website);
    	
    	if (is_object($citiesObject))
			return $citiesObject->getData();
		else
			return false;
    }
}