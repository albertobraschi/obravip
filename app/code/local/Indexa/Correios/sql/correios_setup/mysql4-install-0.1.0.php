<?php
/**
 * Indexa Correios Brasileiros
 *
 * @title      Indexa -> Custom Shipping Extension for Correios Brasileiros
 * @category   Shipping Method
 * @package    Indexa_Correios
 * @author     Gabriel Zamprogna -> desenvolvimento [at] indexainternet [dot] com  [dot] br
 * @copyright  Copyright (c) 2010 Indexa
 */

$installer = $this;
$installer->startSetup();

$result = $installer->getTableRow($installer->getTable('directory_country_region'), 'default_name','Acre');
$start_id = $result["region_id"];

if (is_null($start_id) || !is_numeric($start_id))
{
	$installer->run("
	DELETE FROM {$installer->getTable('directory_country_region')} WHERE country_id = 'BR';
	INSERT INTO {$installer->getTable('directory_country_region')} (country_id, code, default_name) VALUES 
	('BR', 'AC', 'Acre'),
	('BR', 'AL', 'Alagoas'),
	('BR', 'AP', 'Amapá'),
	('BR', 'AM', 'Amazonas'),
	('BR', 'BA', 'Bahia'),
	('BR', 'CE', 'Ceará'),
	('BR', 'ES', 'Espírito Santo'),
	('BR', 'GO', 'Goiás'),
	('BR', 'MA', 'Maranhão'),
	('BR', 'MT', 'Mato Grosso'),
	('BR', 'MS', 'Mato Grosso do Sul'),
	('BR', 'MG', 'Minas Gerais'),
	('BR', 'PA', 'Pará'),
	('BR', 'PB', 'Paraíba'),
	('BR', 'PR', 'Paraná'),
	('BR', 'PE', 'Pernambuco'),
	('BR', 'PI', 'Piauí'),
	('BR', 'RJ', 'Rio de Janeiro'),
	('BR', 'RN', 'Rio Grande do Norte'),
	('BR', 'RS', 'Rio Grande do Sul'),
	('BR', 'RO', 'Rondônia'),
	('BR', 'RR', 'Roraima'),
	('BR', 'SC', 'Santa Catarina'),
	('BR', 'SP', 'São Paulo'),
	('BR', 'SE', 'Sergipe'),
	('BR', 'TO', 'Tocantins'),
	('BR', 'DF', 'Distrito Federal'); ");
	
	$installer->getConnection()
	->commit();
		
	$result = $installer->getConnection()
		->fetchAll("SELECT * FROM {$installer->getTable('directory_country_region')} WHERE default_name = 'Acre'");
	
	if (isset($result[0]["region_id"]))
		$start_id = $result[0]["region_id"];

	if ($start_id > 0)
	{
		$installer->run("
		DELETE FROM {$installer->getTable('directory_country_region_name')} WHERE locale = 'pt_BR';
		
		INSERT INTO {$installer->getTable('directory_country_region_name')} (locale, region_id, name) VALUES 
		('pt_BR', ".$start_id.", 'Acre'),
		('pt_BR', ".(++$start_id).", 'Alagoas'),
		('pt_BR', ".(++$start_id).", 'Amapá'),
		('pt_BR', ".(++$start_id).", 'Amazonas'),
		('pt_BR', ".(++$start_id).", 'Bahia'),
		('pt_BR', ".(++$start_id).", 'Ceará'),
		('pt_BR', ".(++$start_id).", 'Espírito Santo'),
		('pt_BR', ".(++$start_id).", 'Goiás'),
		('pt_BR', ".(++$start_id).", 'Maranhão'),
		('pt_BR', ".(++$start_id).", 'Mato Grosso'),
		('pt_BR', ".(++$start_id).", 'Mato Grosso do Sul'),
		('pt_BR', ".(++$start_id).", 'Minas Gerais'),
		('pt_BR', ".(++$start_id).", 'Pará'),
		('pt_BR', ".(++$start_id).", 'Paraíba'),
		('pt_BR', ".(++$start_id).", 'Paraná'),
		('pt_BR', ".(++$start_id).", 'Pernambuco'),
		('pt_BR', ".(++$start_id).", 'Piauí'),
		('pt_BR', ".(++$start_id).", 'Rio de Janeiro'),
		('pt_BR', ".(++$start_id).", 'Rio Grande do Norte'),
		('pt_BR', ".(++$start_id).", 'Rio Grande do Sul'),
		('pt_BR', ".(++$start_id).", 'Rondônia'),
		('pt_BR', ".(++$start_id).", 'Roraima'),
		('pt_BR', ".(++$start_id).", 'Santa Catarina'),
		('pt_BR', ".(++$start_id).", 'São Paulo'),
		('pt_BR', ".(++$start_id).", 'Sergipe'),
		('pt_BR', ".(++$start_id).", 'Tocantins'),
		('pt_BR', ".(++$start_id).", 'Distrito Federal'); 
		");
		
		$installer->getConnection()
		->commit();
	}
}

$installer->endSetup();