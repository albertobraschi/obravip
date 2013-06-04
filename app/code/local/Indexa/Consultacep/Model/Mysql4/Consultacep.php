<?php

class Indexa_Consultacep_Model_Mysql4_Consultacep extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('indexa_consultacep/consultacep', 'cep');
    }
}