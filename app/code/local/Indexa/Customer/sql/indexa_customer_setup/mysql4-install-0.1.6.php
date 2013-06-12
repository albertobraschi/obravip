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

$installer = $this;

$installer->startSetup();

$attrList   = array();
$idCustomer = 'customer';
$idOrder    = 'order';

$installer->addAttribute($idCustomer, 'cnpj', $attrList);	
$installer->addAttribute($idOrder, 'customer_cnpj', $attrList);	
$installer->addAttribute($idCustomer, 'ie', $attrList);	
$installer->addAttribute($idOrder, 'customer_ie', $attrList);
$installer->addAttribute($idCustomer, 'razao_social', $attrList);	
$installer->addAttribute($idOrder, 'razao_social', $attrList);
$installer->addAttribute($idCustomer, 'faturar_contra', $attrList);	
$installer->addAttribute($idOrder, 'customer_faturar_contra', $attrList);

Mage::getSingleton( 'eav/config' )
    ->getAttribute( 'customer', 'cnpj' )
    ->setData( 'used_in_forms', array( 'adminhtml_checkout','adminhtml_customer','checkout_register','customer_account_create','customer_account_edit' ) )
    ->save();

Mage::getSingleton( 'eav/config' )
    ->getAttribute( 'customer', 'ie' )
    ->setData( 'used_in_forms', array( 'adminhtml_checkout','adminhtml_customer','checkout_register','customer_account_create','customer_account_edit' ) )
    ->save();

Mage::getSingleton( 'eav/config' )
    ->getAttribute( 'customer', 'razao_social' )
    ->setData( 'used_in_forms', array( 'adminhtml_checkout','adminhtml_customer','checkout_register','customer_account_create','customer_account_edit' ) )
    ->save();

Mage::getSingleton( 'eav/config' )
    ->getAttribute( 'customer', 'faturar_contra' )
    ->setData( 'used_in_forms', array( 'adminhtml_checkout','adminhtml_customer','checkout_register','customer_account_create','customer_account_edit' ) )
    ->save();
    
$installer->addColumnIfNotExists($installer->getTable('sales_flat_quote'),'customer_cnpj','TEXT NULL');
$installer->addColumnIfNotExists($installer->getTable('sales_flat_quote_address'),'customer_cnpj','TEXT NULL');
$installer->addColumnIfNotExists($installer->getTable('sales_flat_order'),'customer_cnpj','TEXT NULL');
$installer->addColumnIfNotExists($installer->getTable('sales_flat_order_address'),'customer_cnpj','TEXT NULL');

$installer->addColumnIfNotExists($installer->getTable('sales_flat_quote'),'customer_ie','TEXT NULL');
$installer->addColumnIfNotExists($installer->getTable('sales_flat_quote_address'),'customer_ie','TEXT NULL');
$installer->addColumnIfNotExists($installer->getTable('sales_flat_order'),'customer_ie','TEXT NULL');
$installer->addColumnIfNotExists($installer->getTable('sales_flat_order_address'),'customer_ie','TEXT NULL');

$installer->addColumnIfNotExists($installer->getTable('sales_flat_quote'),'customer_razao_social','TEXT NULL');
$installer->addColumnIfNotExists($installer->getTable('sales_flat_quote_address'),'customer_razao_social','TEXT NULL');
$installer->addColumnIfNotExists($installer->getTable('sales_flat_order'),'customer_razao_social','TEXT NULL');
$installer->addColumnIfNotExists($installer->getTable('sales_flat_order_address'),'customer_razao_social','TEXT NULL');

$installer->addColumnIfNotExists($installer->getTable('sales_flat_quote'),'customer_faturar_contra','TEXT NULL');
$installer->addColumnIfNotExists($installer->getTable('sales_flat_quote_address'),'customer_faturar_contra','TEXT NULL');
$installer->addColumnIfNotExists($installer->getTable('sales_flat_order'),'customer_faturar_contra','TEXT NULL');
$installer->addColumnIfNotExists($installer->getTable('sales_flat_order_address'),'customer_faturar_contra','TEXT NULL');

$installer->endSetup();