<?php

class Indexa_Consultacep_Model_Mysql4_Consultacep_Collection extends Varien_Data_Collection_Db
{
    protected $_consultacepTable;
    protected $_shipTable;
 
    public function __construct()
    {
        $resources = Mage::getSingleton('core/resource');
        parent::__construct($resources->getConnection('indexa_consultacep_read'));
        
        $this->_consultacepTable= $resources->getTableName('indexa_consultacep/consultacep');
        $this->_shipTable = $resources->getTableName('matrixrate_shipping/matrixrate');
    }
    
    /*
     * @TODO Verificar se o algoritmo também funciona com linhas para pesos diferentes, pois isto não é um requisito no VPC. 
     * 
     */
    
    public function getCEPinMatrixRates($cep, $cidades)
    {
    	if (is_numeric($cep))
    	{
    		if (is_array($cidades))
    		{
		        $this->_select->from(array("s" => $this->_shipTable))
		            ->where('s.dest_zip <= ?', $cep)
		            ->where('s.dest_zip_to >= ?', $cep)
		            ->where('s.condition_name = ?', Indexa_Consultacep_Model_Consultacep::TIPO_DE_ENVIO_PADRAO)
		            ->where('s.website_id = ?', Mage::app()->getStore()->getWebsiteId())
		            ->where('s.cidade_id IN (?)',$cidades);
    		}
    		else if (is_numeric($cidades))
    		{
		        $this->_select->from(array("s" => $this->_shipTable))
		            ->where('s.dest_zip <= ?', $cep)
		            ->where('s.dest_zip_to >= ?', $cep)
		            ->where('s.condition_name = ?', Indexa_Consultacep_Model_Consultacep::TIPO_DE_ENVIO_PADRAO)
		            ->where('s.website_id = ?', Mage::app()->getStore()->getWebsiteId())
		            ->where('s.cidade_id = ?',$cidades);
    		}
    		else if ($cidades == false)
    		{
    			$this->_select->from(array("s" => $this->_shipTable))
		            ->where('s.dest_zip <= ?', $cep)
		            ->where('s.dest_zip_to >= ?', $cep)
		            ->where('s.condition_name = ?', Indexa_Consultacep_Model_Consultacep::TIPO_DE_ENVIO_PADRAO)
		            ->where('s.website_id = ?', Mage::app()->getStore()->getWebsiteId());
    		}
    		else 
    		{
    			return false;
    		}
	        
	        $this->_setIdFieldName('pk');
	        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('indexa_consultacep/consultacep'));
	        
	        return $this;
    	}
    	return false;
    }
    
	public function _getBairroinCEP($bairro, $cidades)
    {
    	$bairro = trim($bairro);
    	
        if (!is_array($cidades)) {
            $cidades = array($cidades);
        }
    	
    	if ($bairro == "")
    	{
    		if (is_array($cidades))
    		{
		    	$this->_select
		    		->from(array('c' => $this->_consultacepTable),array('c.cidade','c.cidade_id','c.cep'))->distinct(true)
//		    		->where('upper(c.bairro) LIKE ?', '%'.strtoupper($bairro).'%')
		    		->where('c.cidade_id IN (?)',$cidades)
		    		->order('c.cidade_id', 'asc');
			}
    		else if ($cidades == false)
    		{
    			/*
		    	$this->_select
		    		->from(array('c' => $this->_consultacepTable))
		    		->where('upper(c.bairro) LIKE ?', '%'.strtoupper($bairro).'%');
		    	*/
    			return false;
    		}
    		else
    		{
    			return false;
    		}
    		
	        return $this;
    	}
    	elseif (strlen($bairro) > 1)
    	{
    		if (is_array($cidades))
    		{
		    	$this->_select
		    		->from(array('c' => $this->_consultacepTable),array('c.cidade','c.cidade_id','c.cep'))->distinct(true)
		    		->where('upper(c.bairro) LIKE ?', '%'.strtoupper($bairro).'%')
		    		->where('c.cidade_id IN (?)',$cidades)
		    		->order('c.cidade_id', 'asc');
			}
    		else if ($cidades == false)
    		{
		    	$this->_select
		    		->from(array('c' => $this->_consultacepTable),array('c.cidade','c.cidade_id','c.cep'))->distinct(true)
		    		->where('upper(c.bairro) LIKE ?', '%'.strtoupper($bairro).'%')
		    		->order('c.cidade_id', 'asc');
    		}
    		else
    		{
    			return false;
    		}
    		
	        return $this;
    	}

    	return false;
    }
    
    public function _getBairroRows($cep)
    {
        $this->_select->from(array("s" => $this->_shipTable))
            ->where('s.dest_zip <= ?', $cep)
            ->where('s.dest_zip_to >= ?', $cep)
            ->where('s.condition_name = ?', Indexa_Consultacep_Model_Consultacep::TIPO_DE_ENVIO_PADRAO)
            ->where('s.website_id = ?', Mage::app()->getStore()->getWebsiteId());
        
        $this->_setIdFieldName('pk');
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('indexa_consultacep/consultacep'));
        
        return $this;
    }
    
    public function getBairroinMatrixRates($bairro, $cidades)
    {
    	$data = array();
    	
    	/*
    	 * @TODO
    	 * Descomentar  a linha abaixo para não acetar bairro vazio?
    	 * 
    	 */
    	
    	if (is_string($bairro) /* && $bairro != '' && strlen(trim($bairro)) > 0 */)
    	{
    		$collection = Mage::getModel('indexa_consultacep/consultacep')->getCollection();
    		$bairroEmCeps = $collection->_getBairroinCEP($bairro, $cidades);
    		
    		if (is_object($bairroEmCeps))
    		{
    			$data = $bairroEmCeps->getData();
    		}
    	}
    	
    	/*
    	 * Pega o primeiro CEP de cada cidade
    	 * 
    	 */
    	
    	$previousCity = "";
    	$data_cep_cidade = array();
    	for ($i = 0; $i < count($data); $i++)
    	{
    		if ($previousCity != $data[$i]['cidade_id'])
    		{
    			$data_cep_cidade[] = $data[$i];
    			$previousCity = $data[$i]['cidade_id'];
    		}
    		else
    		{
    			continue;
    		}
    	}
    	
    	// cada um dos resultados dos ceps encontrados
    	$data_acumulativo = array();
    	$entrou = false;
    	
    	foreach($data_cep_cidade as $value)
    	{
    		$entrou = true;
    		
    		$collection = Mage::getModel('indexa_consultacep/consultacep')->getCollection();
    		$data_acumulativo[] = $collection->_getBairroRows($value['cep'])->getData();
    	}
    	
    	if ($entrou) return $data_acumulativo;
    	
    	return false;
    }
    
    public function getLocationByCEP($cep)
    {
    	if (is_numeric($cep))
    	{
	    	$this->_select->from(array('c' => $this->_consultacepTable))->where('c.cep = ?', $cep);
	        
	        $this->_setIdFieldName('pk');
	        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('indexa_consultacep/consultacep'));
	        
	        return $this;
    	}
    	return false;
    }
    
    public function getLocationByCidade($cidade)
    {
    	if (is_string($cidade))
    	{
	    	$this->_select->from(array('c' => $this->_consultacepTable))->where('upper(c.cidade) LIKE ?', '%'.strtoupper($cidade).'%');
	        
	        $this->_setIdFieldName('pk');
	        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('indexa_consultacep/consultacep'));
	        
	        return $this;
    	}
    	return false;
    }
    
    public function getCityInfoByCepRange($cepFrom, $cepTo)
    {
    	if (is_numeric($cepFrom) && is_numeric($cepTo))
    	{
	    	$this->_select
		    	->from(array('c' => $this->_consultacepTable),array('c.cidade_id', 'c.cep', 'c.cidade', 'c.bairro'))->distinct(true)
		    	->where('cep >= ?', $cepFrom)
		    	->where('cep <= ?', $cepTo)
		    	->limit(1,0);
	        
	        $this->_setIdFieldName('pk');
	        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('indexa_consultacep/consultacep'));
	        
	        return $this;
    	}
    	return false;
    }  

    public function _getCitiesIds($website)
    {
    	if (is_numeric($website))
    	{
    		
	    	$this->_select
		    	->from(array('c' => $this->_shipTable),'c.cidade_id')
		    	->distinct(true)
		    	->where('c.website_id = ?', $website);
		    
	        $this->_setIdFieldName('pk');
	        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('indexa_consultacep/consultacep'));
	        
	        return $this;
    	}
    	return false;
    }
    
    public function getMatrixRatesCities($website)
    {
    	if (is_numeric($website))
    	{
    		$collection = Mage::getModel('indexa_consultacep/consultacep')->getCollection();
    		$data = $collection->_getCitiesIds($website)->getData();
			
    		$data_final = array();
    		foreach ($data as $value)
    		{
    			$data_final[] = $value['cidade_id'];
    		}
    		
	    	$this->_select
		    	->from(array('c' => $this->_consultacepTable),array('c.cidade','c.cidade_id'))
		    	->distinct(true)
		    	->where('cidade_id IN (?)', $data_final);
		    
	        $this->_setIdFieldName('pk');
	        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('indexa_consultacep/consultacep'));
	        
	        return $this;
    	}
    	return false;
    }
    
    public function getDeliveryTypeText($cep)
    {
    	if (is_numeric($cep))
    	{
	    	$this->_select
		    	->from(array('s' => $this->_shipTable),
		    			array('s.bairro','s.cidade'))
		    	->where('s.dest_zip = ?', $cep)
	            ->where('s.condition_name = ?', Indexa_Consultacep_Model_Consultacep::TIPO_DE_ENVIO_PADRAO)
	            ->where('s.website_id = ?', Mage::app()->getStore()->getWebsiteId());
	        
	        $this->_setIdFieldName('pk');
	        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('indexa_consultacep/consultacep'));
	        
	        return $this;
    	}
    	return false;
    }  
}