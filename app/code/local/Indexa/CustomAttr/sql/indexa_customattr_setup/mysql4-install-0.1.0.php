<?php

$installer = $this;
$installer->startSetup();
$setup = Mage::getModel('customer/entity_setup', 'core_setup');
$setup->addAttribute('customer', 'designer_arquiteto', array(
    'type' => 'int',
    'input' => 'select',
    'label' => 'Designer/Arquiteto',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'default' => '0',
    'visible_on_front' => 1,
	'source'=> 'adminhtml/system_config_source_yesno'
));

$forms =  array(
				'customer_register_address',
				'customer_account_create',
				'customer_account_edit',
				'adminhtml_customer'
				);

Mage::getSingleton('eav/config')
	->getAttribute('customer', 'designer_arquiteto')
	->setData('used_in_forms', $forms)
    ->save();
    
$installer->endSetup();