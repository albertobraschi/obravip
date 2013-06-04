<?php
class Indexa_Consultacep_Block_Mapa extends Mage_Core_Block_Template
{

	/*
	 * 
	 * Já que algoritmo é o memso para juntar os ids e pegar a frase
	 * é bom manter um cache dos dados pra quem rodar primeiro.
	 * 
	 */
	protected $_fraseCache = "";
	protected $_idsCache = "";
	
	protected $_cidades;
	protected $_count = false;
	
    protected function _construct()
    {
        parent::_construct();
		
		$this->_cidades = Mage::getModel('indexa_consultacep/consultacep')->retrieveCityListByWebsite();
		
        if (is_array($this->_cidades)) // Lógica deste count pode mudar??
    	{
			$this->_count = count($this->_cidades);
    	}
    }
    
    public function getCityMessage ()
    {
    	if (strlen($this->_fraseCache) > 0)
    	{
    		return $this->_fraseCache;
    	}
    	
    	$array_ids = array();
    	
		for($i = 0; $i < $this->_count; $i++)
		{
			if (array_key_exists('cidade', $this->_cidades[$i]))
			{
				$value = $this->_cidades[$i]['cidade'];
				$array_ids[] = $this->_cidades[$i]['cidade_id'];
			
				if ($i == 0) { $this->_fraseCache .= $this->__('Esta empresa entrega em: '); }
				
				if ($i < $this->_count-1) // Empresas intermediárias
				{
					$this->_fraseCache .= $value.', ';
				}
				
				if ($i == $this->_count-1) // Última com ponto final
				{
					$this->_fraseCache  .= $value.'.';
				}
			}
		}
		
		$this->_idsCache = implode(',',$array_ids);
		
		return $this->_fraseCache;
	}
	
    public function getCityIdsImploded ()
    {
    	if (strlen($this->_idsCache) > 0)
    	{
    		return $this->_idsCache;
    	}

    	$array_ids = array();
    	
		for($i = 0; $i < $this->_count; $i++)
		{
			if (array_key_exists('cidade', $this->_cidades[$i]))
			{
				$value = $this->_cidades[$i]['cidade'];
				$array_ids[] = $this->_cidades[$i]['cidade_id'];
			
				if ($i == 0) { $this->_fraseCache .= $this->__('Esta empresa entrega em: '); }
				
				if ($i < $this->_count-1) // Empresas intermediárias
				{
					$this->_fraseCache .= $value.', ';
				}
				
				if ($i == $this->_count-1) // Última com ponto final
				{
					$this->_fraseCache  .= $value.'.';
				}
			}
		}
		
		$this->_idsCache = implode(',',$array_ids);
		
		return $this->_idsCache;
	}
	
	public function getCityCount ()
    {
    	return $this->_count;
	}
}