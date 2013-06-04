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

class Indexa_Customer_Model_Entity_Setup extends Mage_Customer_Model_Entity_Setup
{
	public function addColumnIfNotExists ($table, $column, $info)
	{
		/* @var $connection Varien_Db_Adapter_Pdo_Mysql */
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$results = $connection->query("SHOW COLUMNS FROM {$table};");
		
		$found = false; 
		foreach ($results as $result)
		{
			if ($result['Field'] == $column) $found = true;
		}
		
		$results_alter = false;
		if (!$found) { $results_alter = $connection->query("ALTER TABLE {$table} ADD COLUMN {$column} {$info};"); }
	}

    public function getDefaultEntities()
    {
        return array(
            'customer' => array(
                'entity_model'          =>'customer/customer',
                'table'                 => 'customer/entity',
                'increment_model'       => 'eav/entity_increment_numeric',
                'increment_per_store'   => false,
                'attributes' => array(
                    'website_id' => array(
                        'type'          => 'static',
                        'label'         => 'Associate to Website',
                        'input'         => 'select',
                        'source'        => 'customer/customer_attribute_source_website',
                        'backend'       => 'customer/customer_attribute_backend_website',
                        'sort_order'    => 10,
                    ),
                    'store_id' => array(
                        'type'          => 'static',
                        'label'         => 'Create In',
                        'input'         => 'select',
                        'source'        => 'customer/customer_attribute_source_store',
                        'backend'       => 'customer/customer_attribute_backend_store',
                        'visible'       => false,
                        'sort_order'    => 20,
                    ),
                    'created_in' => array(
                        'type'          => 'varchar',
                        'label'         => 'Created From',
                        'sort_order'    => 30,
                    ),
                    'prefix' => array(
                        'label'         => 'Prefix',
                        'required'      => false,
                        'sort_order'    => 37,
                    ),
                    'firstname' => array(
                        'label'         => 'First Name',
                        'sort_order'    => 40,
                    ),
                    'middlename' => array(
                        'label'         => 'Middle Name/Initial',
                        'required'      => false,
                        'sort_order'    => 43,
                    ),
                    'lastname' => array(
                        'label'         => 'Last Name',
                        'sort_order'    => 50,
                    ),
                    'suffix' => array(
                        'label'         => 'Suffix',
                        'required'      => false,
                        'sort_order'    => 53,
                    ),
                    'email' => array(
                        'type'          => 'static',
                        'label'         => 'Email',
                        'class'         => 'validate-email',
                        'sort_order'    => 60,
                    ),
					'faturar_contra' => array(
						'label'         => 'Faturar Contra Empresa?',
						'required'      => true,
//						'required'      => true,
                    	'class'         => 'validate-faturar_contra',
						'sort_order'    => 62,
					),
					'razao_social' => array(
						'label'         => 'Razão Social',
						'required'      => false,
//						'required'      => true,
                    	'class'         => 'validate-razao_social',
						'sort_order'    => 63,
					),
					'cnpj' => array(
						'label'         => 'CNPJ',
						'required'      => false,
//						'required'      => true,
                    	'class'         => 'validate-cnpj',
						'sort_order'    => 64,
					),
					'ie' => array(
						'label'         => 'IE',
						'required'      => false,
//						'required'      => true,
                    	'class'         => 'validate-ie',
						'sort_order'    => 65,
					),
                    'group_id' => array(
                        'type'          => 'static',
                        'input'         => 'select',
                        'label'         => 'Customer Group',
                        'source'        => 'customer/customer_attribute_source_group',
                        'sort_order'    => 70,
                    ),
                    'dob' => array(
                        'type'          => 'datetime',
                        'input'         => 'date',
                        'backend'       => 'eav/entity_attribute_backend_datetime',
                        'required'      => false,
                        'label'         => 'Date Of Birth',
                        'sort_order'    => 80,
                    ),
                    'password_hash' => array(
                        'input'         => 'hidden',
                        'backend'       => 'customer/customer_attribute_backend_password',
                        'required'      => false,
                    ),
                    'default_billing' => array(
                        'type'          => 'int',
                        'visible'       => false,
                        'required'      => false,
                        'backend'       => 'customer/customer_attribute_backend_billing',
                    ),
                    'default_shipping' => array(
                        'type'          => 'int',
                        'visible'       => false,
                        'required'      => false,
                        'backend'       => 'customer/customer_attribute_backend_shipping',
                    ),
                    'taxvat' => array(
                        'label'         => 'Tax/VAT number',
                        'visible'       => true,
                        'required'      => false,
                        'position'      => 1,
                    ),
                    'confirmation' => array(
                        'label'         => 'Is confirmed',
                        'visible'       => false,
                        'required'      => false,
                    ),
                ),
            ),

            'customer_address'=>array(
                'entity_model'  =>'customer/customer_address',
                'table' => 'customer/address_entity',
                'attributes' => array(
                    'prefix' => array(
                        'label'         => 'Prefix',
                        'required'      => false,
                        'sort_order'    => 7,
                    ),
                    'firstname' => array(
                        'label'         => 'First Name',
                        'sort_order'    => 10,
                    ),
                    'middlename' => array(
                        'label'         => 'Middle Name/Initial',
                        'required'      => false,
                        'sort_order'    => 13,
                    ),
                    'lastname' => array(
                        'label'         => 'Last Name',
                        'sort_order'    => 20,
                    ),
                    'suffix' => array(
                        'label'         => 'Suffix',
                        'required'      => false,
                        'sort_order'    => 23,
                    ),
                    'company' => array(
                        'label'         => 'Company',
                        'required'      => false,
                        'sort_order'    => 30,
                    ),
                    'street' => array(
                        'type'          => 'text',
                        'backend'       => 'customer_entity/address_attribute_backend_street',
                        'input'         => 'multiline',
                        'label'         => 'Street Address',
                        'sort_order'    => 40,
                    ),
                    'city' => array(
                        'label'         => 'City',
                        'sort_order'    => 50,
                    ),
                    'country_id' => array(
                        'type'          => 'varchar',
                        'input'         => 'select',
                        'label'         => 'Country',
                        'class'         => 'countries',
                        'source'        => 'customer_entity/address_attribute_source_country',
                        'sort_order'    => 60,
                    ),
                    'region' => array(
                        'backend'       => 'customer_entity/address_attribute_backend_region',
                        'label'         => 'State/Province',
                        'class'         => 'regions',
                        'sort_order'    => 70,
                    ),
                    'region_id' => array(
                        'type'          => 'int',
                        'input'         => 'hidden',
                        'source'        => 'customer_entity/address_attribute_source_region',
                        'required'      => 'false',
                        'sort_order'    => 80,
                    ),
                    'postcode' => array(
                        'label'         => 'Zip/Postal Code',
                        'sort_order'    => 90,
                    ),
                    'telephone' => array(
                        'label'         => 'Telephone',
                        'sort_order'    => 100,
                    ),
                    'fax' => array(
                        'label'         => 'Fax',
                        'required'      => false,
                        'sort_order'    => 110,
                    ),
                ),
            ),
        );
    }

}
