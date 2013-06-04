<?php
/**
 * Indexa - Customer. Adicionar campos ao checkout.
 *
 * @title      Magento -> Indexa Customer module
 * @category   Custom customer fields
 * @package    Indexa_Customer
 * @author     Indexa Development Team -> desenvolvimento [at] indexainternet [dot] com  [dot] br
 * @copyright  Copyright (c) 2011 Indexa - http://www.indexainternet.com.br
 */

class Indexa_Customer_Model_Customer extends Mage_Customer_Model_Customer
{
    /**
     * Validate customer CNPJ
     * Sandro J. S. Souza / xKuRt
     * http://www.htmlstaff.org/ver.php?id=6414
     * c/ uma melhorada da Indexa
     * 
     * @return bool
     */
	public function validaCnpj($cnpj)
	{
		$cnpj = preg_replace("/[^0-9]/", "", $cnpj); 
	    if (strlen($cnpj) == 14)
	    {
	        for ($t = 12; $t < 14; $t++)
	        {
	            for ($d = 0, $p = $t - 7, $c = 0; $c < $t; $c++)
	            {
	                $d += $cnpj{$c} * $p;
	                $p   = ($p < 3) ? 9 : --$p;
	            }
	
	            $d = ((10 * $d) % 11) % 10;
	
	            if ($cnpj{$c} != $d)
	            {
	                return false;
	            }
	        }
	        return true;
	    }	    
	    return false;
	}
	
	public function validaIe($ie)
	{
		if (strlen($ie) > 0)
			return true;

	    return false;
	}
	
	public function validaCpf($cpf)
	{	// Verifiva se o número digitado contém todos os digitos
	    $cpf = str_pad(ereg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);
		
		// Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
	    if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999')
		{
		return false;
	    }
		else
		{   // Calcula os números para verificar se o CPF é verdadeiro
	        for ($t = 9; $t < 11; $t++) {
	            for ($d = 0, $c = 0; $c < $t; $c++) {
	                $d += $cpf{$c} * (($t + 1) - $c);
	            }
	
	            $d = ((10 * $d) % 11) % 10;
	
	            if ($cpf{$c} != $d) {
	                return false;
	            }
	        }
	
	        return true;
	    }
	}
	
	public function validaRazaoSocial($razao)
	{
		if (strlen($razao) > 0)
			return true;

	    return false;
	}
	
		
    /**
     * Validate customer attribute values
     *
     * @return bool
     */
    public function validate()
    {
    	parent::validate();
    	
        $errors = array();
        
        if ($this->getFaturarContra() == "1")
        {
	        if (!$this->validaRazaoSocial($this->getRazaoSocial())) {
	            $errors[] = Mage::helper('customer')->__('RAZãO SOCIAL is empty or incorrect');
	        }
        	if (!$this->validaCnpj($this->getCnpj())) {
	            $errors[] = Mage::helper('customer')->__('CNPJ is empty or incorrect');
	        }
	        if (!$this->validaIe($this->getIe())) {
	            $errors[] = Mage::helper('customer')->__('IE is empty or incorrect');
	        }
	        if (!$this->validaCpf($this->getTaxvat())) {
	            $errors[] = Mage::helper('customer')->__('CPF is empty or incorrect');
	        }
        }
        
        if (empty($errors))
        {
            return true;
        }
        return $errors;
    }
}