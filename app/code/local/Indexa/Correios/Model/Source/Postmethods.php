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

class Indexa_Correios_Model_Source_PostMethods
{

    public function toOptionArray()
    {
		// METODOS A SE VERIFICAR
		// 41106 PAC sem contrato
		// 40010 SEDEX sem contrato
		// 40045 SEDEX a Cobrar, sem contrato
		// 40215 SEDEX 10, sem contrato
		// 40290 SEDEX Hoje, sem contrato
		// 40096 SEDEX com contrato
		// 40436 SEDEX com contrato
		// 40444 SEDEX com contrato
		// 81019 e-SEDEX, com contrato
		// 41068 PAC com contrato
		
        return array(
        	array('value'=>0, 'label'=>Mage::helper('adminhtml')->__('Não Permitir')),
            array('value'=>41106, 'label'=>Mage::helper('adminhtml')->__('PAC sem contrato')),
            array('value'=>40010, 'label'=>Mage::helper('adminhtml')->__('SEDEX sem contrato')),
            array('value'=>40045, 'label'=>Mage::helper('adminhtml')->__('SEDEX a Cobrar, sem contrato')),
            array('value'=>40215, 'label'=>Mage::helper('adminhtml')->__('SEDEX 10, sem contrato')),
            array('value'=>40290, 'label'=>Mage::helper('adminhtml')->__('SEDEX Hoje, sem contrato')),
        );
    }
}