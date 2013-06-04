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

class Indexa_Customer_Block_Rewrite_AdminEditTabAccount extends Mage_Adminhtml_Block_Customer_Edit_Tab_Account
{
	public function initForm()
    {
		$parentr = parent::initForm();

		$parentr->getForm()->getElement('base_fieldset')->removeField('faturar_contra');
		$parentr->getForm()->getElement('base_fieldset')->addField('faturar_contra', 'select',
                array(
                    'label' => Mage::helper('customer')->__('Faturar Contra Empresa?'),
                    'class' => 'input-text required-entry validate-faturar_contra',
                    'name'  => 'faturar_contra',
                    'required' => false,
                	'value' => Mage::registry('current_customer')->getFaturarContra()
                )
            )->setValues(array ('1' => 'Sim', '0' => 'Não')); 

		$parentr->getForm()->getElement('base_fieldset')->removeField('razao_social');
		$parentr->getForm()->getElement('base_fieldset')->addField('razao_social', 'text',
                array(
                    'label' => Mage::helper('customer')->__('Razão Social'),
                    'class' => 'input-text required-entry validate-razao_social',
                    'name'  => 'razao_social',
                    'required' => false,
                	'value' => Mage::registry('current_customer')->getRazaoSocial()
                )
            );

		$parentr->getForm()->getElement('base_fieldset')->removeField('cnpj');
		$parentr->getForm()->getElement('base_fieldset')->addField('cnpj', 'text',
                array(
                    'label' => Mage::helper('customer')->__('CNPJ'),
                    'class' => 'input-text required-entry validate-cnpj',
                    'name'  => 'cnpj',
                    'required' => false,
                	'value' => Mage::registry('current_customer')->getCnpj()
                )
            );
            
        $parentr->getForm()->getElement('base_fieldset')->removeField('ie');
		$parentr->getForm()->getElement('base_fieldset')->addField('ie', 'text',
                array(
                    'label' => Mage::helper('customer')->__('IE'),
                    'class' => 'input-text required-entry validate-ie',
                    'name'  => 'ie',
                    'required' => false,
                	'value' => Mage::registry('current_customer')->getIe()
                )
            );
            
		return $parentr;
    }
}