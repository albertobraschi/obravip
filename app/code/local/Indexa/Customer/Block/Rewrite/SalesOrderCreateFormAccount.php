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

class Indexa_Customer_Block_Rewrite_SalesOrderCreateFormAccount extends Mage_Adminhtml_Block_Sales_Order_Create_Form_Account
{
    public function getDisplayFields()
    {
        $fields = array(
            'group_id' => array(
                'order' => 1
            ),
            'email' => array(
                'order' => 2,
                'class' => 'validate-email',
                'required' => false
            ),
            'faturar_contra' => array(
                'order' => 6,
                'class' => 'validate-faturar_contra',
                'required' => false
            ),
            'razao_social' => array(
                'order' => 9,
                'class' => 'validate-razao_social',
                'required' => false
            ),
            'cnpj' => array(
                'order' => 10,
                'class' => 'validate-cnpj',
                'required' => false
            ),
            'ie' => array(
                'order' => 15,
                'class' => 'validate-ie',
                'required' => false
            )
        );

        if ($this->getQuote()->getCustomerIsGuest()) {
            unset($fields['group_id']);
        }

        return $fields;
    }
    
    public function getForm()
    {
    	$form = parent::getForm();
    	    	
        $form->getElement('main')->removeField('faturar_contra');
    	$form->getElement('main')->addField('faturar_contra', 'select',
                array(
                    'label' => Mage::helper('customer')->__('Faturar Contra Empresa?'),
                    'class' => 'validate-faturar_contra',
                    'name'  => 'order[account][faturar_contra]',
                    'required' => true
                )
            )->setValues(array ('1' => 'Sim', '0' => 'Não'));

        $form->getElement('main')->removeField('razao_social');
    	$form->getElement('main')->addField('razao_social', 'text',
                array(
                    'label' => Mage::helper('customer')->__('Razão Social'),
                    'class' => 'validate-razao_social',
                    'name'  => 'order[account][razao_social]',
//                    'required' => true
                )
            );

        $form->getElement('main')->removeField('cnpj');
    	$form->getElement('main')->addField('cnpj', 'text',
                array(
                    'label' => Mage::helper('customer')->__('CNPJ'),
                    'class' => 'validate-cnpj',
                    'name'  => 'order[account][cnpj]',
//                    'required' => true
                )
            );
            
        $form->getElement('main')->removeField('ie');
    	$form->getElement('main')->addField('ie', 'text',
                array(
                    'label' => Mage::helper('customer')->__('IE'),
                    'class' => 'validate-ie',
                    'name'  => 'order[account][ie]',
//                    'required' => true
                )
            );
        
		$form->setValues($this->getCustomerData());
		return $form;
    }
    
    public function getCustomerData()
    {
        $data = $this->getCustomer()->getData();
        foreach ($this->getQuote()->getData() as $key=>$value) {
        	if (strstr($key, 'customer_')) {
        	    $data[str_replace('customer_', '', $key)] = $value;
        	}
        }
        
        $data['group_id'] = $this->getCreateOrderModel()->getCustomerGroupId();
        $data['email']    = ($this->getQuote()->getCustomerEmail() ? $this->getQuote()->getCustomerEmail() : $this->getCustomer()->getData('email'));        
        $data['faturar_contra']	= ($this->getQuote()->getCustomerFaturarContra() ? $this->getQuote()->getCustomerFaturarContra() : $this->getCustomer()->getData('faturar_contra'));
        $data['razao_social']	= ($this->getQuote()->getCustomerRazaoSocial() ? $this->getQuote()->getCustomerRazaoSocial() : $this->getCustomer()->getData('razao_social'));
        $data['cnpj']     = ($this->getQuote()->getCustomerCnpj() ? $this->getQuote()->getCustomerCnpj() : $this->getCustomer()->getData('cnpj'));
        $data['ie']		  = ($this->getQuote()->getCustomerIe() ? $this->getQuote()->getCustomerIe() : $this->getCustomer()->getData('ie'));
                
        return $data;
    }
}
