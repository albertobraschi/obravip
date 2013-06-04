<?php

class Indexa_Installments_Model_Auto extends Indexa_Installments_Model_Abstract {

    public function getMaxParcelas() {
        return Mage::getStoreConfig('indexa/installments/n_max_parcelas');
    }

}
