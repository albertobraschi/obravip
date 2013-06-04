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

class Indexa_Customer_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
	public function saveBilling($data, $customerAddressId)
	{			
		// Verificar se o CNPJ vem da variavel data
		// Verificar se emissão é para PJ
		//$cnpj = $data['cnpj'];
		
		if (array_key_exists('faturar_contra', $data))
		{
			if (!empty( $data['faturar_contra'] ) && $data['faturar_contra'] == '1')
			{
				if( !empty( $data['faturar_contra'] ) )
				{
					$this->getQuote()->setCustomerFaturarContra($data['faturar_contra'])->save();
					$this->getQuote()->getBillingAddress()->setCustomerFaturarContra($data['faturar_contra'])->save();				
				}

				if( !empty( $data['razao_social'] ) )
				{
					$this->getQuote()->setCustomerRazaoSocial($data['razao_social'])->save();
					$this->getQuote()->getBillingAddress()->setCustomerRazaoSocial($data['razao_social'])->save();			
				}
				else
				{
					return array('error' => 1, 'message' => Mage::helper('indexa_customer')->__('Invalid RAZãO SOCIAL "%s"', $data['razao_social']));
				}

				if( !empty( $data['cnpj'] ) )
				{
					$this->getQuote()->setCustomerCnpj($data['cnpj'])->save();
					$this->getQuote()->getBillingAddress()->setCustomerCnpj($data['cnpj'])->save();			
				}
				else
				{
					return array('error' => 1, 'message' => Mage::helper('indexa_customer')->__('Invalid CNPJ "%s"', $data['cnpj']));
				}
				
				if( !empty( $data['ie'] ) )
				{
					$this->getQuote()->setCustomerIe($data['ie'])->save();
					$this->getQuote()->getBillingAddress()->setCustomerIe($data['ie'])->save();					
				}
				else
				{
					return array('error' => 1, 'message' => Mage::helper('indexa_customer')->__('Invalid Inscrição Estadual "%s"', $data['ie']));
				}
/*				
				if( empty( $data['company'] ) )
				{
					return array('error' => 1, 'message' => Mage::helper('indexa_customer')->__('Invalid Company "%s"', $data['company']));
				}
*/				
			}
		}
		
		return parent::saveBilling($data, $customerAddressId);
	}
	
    protected function _processValidateCustomer(Mage_Sales_Model_Quote_Address $address)
    {    	
        // set customer date of birth for further usage
        $dob = '';
        if ($address->getDob()) {
            $dob = Mage::app()->getLocale()->date($address->getDob(), null, null, false)->toString('yyyy-MM-dd');
            $this->getQuote()->setCustomerDob($dob);
        }

        // set customer tax/vat number for further usage
        if ($address->getTaxvat()) {
            $this->getQuote()->setCustomerTaxvat($address->getTaxvat());
        }

        // invoke customer model, if it is registering
        if (Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()) {
            // set customer password hash for further usage
            $customer = Mage::getModel('customer/customer');
            $this->getQuote()->setPasswordHash($customer->encryptPassword($address->getCustomerPassword()));

            // validate customer
            foreach (array(
                'firstname'    		=> 'firstname',
                'lastname'     		=> 'lastname',
                'email'        		=> 'email',
                'password'     		=> 'customer_password',
                'confirmation' 		=> 'confirm_password',
                'taxvat'       		=> 'taxvat',
            	'faturar_contra'	=> 'faturar_contra',
            	'razao_social'		=> 'razao_social',
            	'cnpj'         		=> 'cnpj',
            	'ie'         		=> 'ie',
            ) as $key => $dataKey) {
                $customer->setData($key, $address->getData($dataKey));
            }
            if ($dob) {
                $customer->setDob($dob);
            }
            $validationResult = $customer->validate();
            if (true !== $validationResult && is_array($validationResult)) {
                return array(
                    'error'   => -1,
                    'message' => implode(', ', $validationResult)
                );
            }
        } elseif(Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST == $this->getQuote()->getCheckoutMethod()) {
            $email = $address->getData('email');
            if (!Zend_Validate::is($email, 'EmailAddress')) {
                return array(
                    'error'   => -1,
                    'message' => Mage::helper('checkout')->__('Invalid email address "%s"', $email)
                );
            }
        }

        return true;
    }

	public function saveOrder()
	{				
	    $this->validateOrder();
        $billing = $this->getQuote()->getBillingAddress();
        if (!$this->getQuote()->isVirtual()) {
            $shipping = $this->getQuote()->getShippingAddress();
        }
		
		switch ($this->getQuote()->getCheckoutMethod())
		{
        	case Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER:
				if ($this->getQuote()->getCustomerFaturarContra() && !$billing->getCustomerFaturarContra())
				{
				    $billing->setCustomerFaturarContra($this->getQuote()->getCustomerFaturarContra());
				}
				if ($this->getQuote()->getCustomerRazaoSocial() && !$billing->getCustomerRazaoSocial())
				{
				    $billing->setCustomerRazaoSocial($this->getQuote()->getCustomerRazaoSocial());
				}
				if ($this->getQuote()->getCustomerCnpj() && !$billing->getCustomerCnpj())
				{
				    $billing->setCustomerCnpj($this->getQuote()->getCustomerCnpj());
				}
				if ($this->getQuote()->getCustomerIe() && !$billing->getCustomerIe())
				{
				    $billing->setCustomerIe($this->getQuote()->getCustomerIe());
				}
        	break;
		}

		return parent::saveOrder();
	}
	
	public function validate()
	{
		parent::validate();
	}
}