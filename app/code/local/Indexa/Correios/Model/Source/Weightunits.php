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

class Indexa_Correios_Model_Source_Weightunits
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1000, 'label'=>Mage::helper('adminhtml')->__('Gramas')),
            array('value'=>1, 'label'=>Mage::helper('adminhtml')->__('Quilogramas')),
        );
    }

}