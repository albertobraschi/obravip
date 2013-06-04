<?php

/**
 * Indexa + Conversão Module
 *
 * @title      Magento -> + Conversão Module
 * @category   Payment Gateway
 * @package    Indexa_Mc
 * @author     Indexa Team -> desenvolvimento [at] indexainternet [dot] com [dot] br
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @copyright  Copyright (c) 2011 Indexa - http://www.indexainternet.com.br
 */
$installer = $this;
$installer->startSetup();

$installer->run('
    ALTER TABLE sales_flat_quote_address ADD COLUMN juros_amount DECIMAL(12,4); 
    ALTER TABLE sales_flat_quote_address ADD COLUMN base_juros_amount DECIMAL(12,4); 

    ALTER TABLE sales_flat_order ADD COLUMN juros_amount DECIMAL(12,4); 
    ALTER TABLE sales_flat_order ADD COLUMN base_juros_amount DECIMAL (12,4); 

    ALTER TABLE sales_flat_invoice ADD COLUMN juros_amount DECIMAL(12,4); 
    ALTER TABLE sales_flat_invoice ADD COLUMN base_juros_amount DECIMAL(12,4); 

    ALTER TABLE sales_flat_creditmemo ADD COLUMN juros_amount DECIMAL(12,4); 
    ALTER TABLE sales_flat_creditmemo ADD COLUMN base_juros_amount DECIMAL(12,4); 
    
    ALTER TABLE sales_flat_order_payment ADD COLUMN cc_parcelas INT(11) DEFAULT null;
    ALTER TABLE sales_flat_quote_payment ADD COLUMN cc_parcelas INT(11) DEFAULT null;
    
    ALTER TABLE sales_flat_order_payment ADD COLUMN gwap_boleto_type VARCHAR(255) DEFAULT NULL;
    ALTER TABLE sales_flat_quote_payment ADD COLUMN gwap_boleto_type VARCHAR(255) DEFAULT NULL;
');

$installer->endSetup();
