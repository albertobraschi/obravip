<?php

$installer = $this;
$installer->startSetup ();


$config = Mage::getModel('indexa_listcolor/config');

$attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')
->addFieldToFilter('entity_type_id', array('eq' => 4))
->load();



/*
 * Create attribute for indexing
 */

$entityTypeId = $installer->getEntityTypeId ( 'catalog_product' );
$attributeSetId = $installer->getDefaultAttributeSetId ( $entityTypeId );
$attributeGroupId = $installer->getDefaultAttributeGroupId ( $entityTypeId, $attributeSetId );


$glue_id = $config->getGlueId();
$installer->addAttribute('catalog_product', $glue_id, array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Supplier Id',
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => '',
        'is_configurable'   => false
    ));


$other_colors_id = $config->getOtherColors();
$installer->addAttribute('catalog_product', $other_colors_id, array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Other colors of this product',
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'           => false,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => false,
        'filterable'        => false,
        'comparable'        => false,
        'visible_on_front'  => false,
        'unique'            => false,
        'apply_to'          => '',
        'is_configurable'   => false
    ));

foreach($attributeSetCollection as $set)
{
	$installer->addAttributeToGroup ( $entityTypeId, $set->getId(), $attributeGroupId, $glue_id, '10' );
	$installer->addAttributeToGroup ( $entityTypeId, $set->getId(), $attributeGroupId, $other_colors_id, '10' );
}



/*
 * FIX Attributes visibility
 */

$list = $config->getAttributesList();
$model = Mage::getModel('catalog/resource_eav_attribute');

foreach($list as $id)
{
	$model->clearInstance()->load(
		$id,
		Indexa_Listcolor_Model_Config::LOAD_BY
	);

	if ($model->getId() > 0)
	{
		$model->setUsedInProductListing(1)->save();
	}
	else
	{
		Mage::throwException('Invalid attribute code: ' . $id);
	}
}

/* Invalidar índice para indicar que reindex deve ser refeito
 * Mas após alguns testes percebeu-se que esse disparo já é automático
 * Ao se salvar um atributo da EAV
 **/ 
//$indexer = Mage::getSingleton('index/indexer');
//$process = $indexer->getProcessByCode(Indexa_Listcolor_Model_Productcollection::INDEX_CODE_TO_INVALID);
//$process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);



$installer->endSetup ();