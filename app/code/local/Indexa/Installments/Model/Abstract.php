<?php

class Indexa_Installments_Model_Abstract extends Varien_Object {

    
    private $_value;
    private $_installmentsArray;
    
    public function isActive() {
        return Mage::getStoreConfig('indexa/installments/active') ? true : false;
    }

    /*
     * System->Configuration getters
     */

    public function getMessage($juros) {
        if ($juros > 0) {
            return sprintf(Mage::getStoreConfig('indexa/installments/mensagem_padrao_comjuros'), number_format($juros, 2) . '%' );
        } else {
            return Mage::getStoreConfig('indexa/installments/mensagem_padrao_semjuros');
        }
    }

    public function getParcelaConfigurationArray() {
        if (!$this->_installmentsArray) {
            $this->_installmentsArray = unserialize(Mage::getStoreConfig('indexa/installments/parcelas'));
        }
        return $this->_installmentsArray;
    }
    
    
    public function getParcelaMinima() {
        return Mage::getStoreConfig('indexa/installments/parcela_minima');
    }
    
    public function getParcelaSemJuros() {
        return Mage::getStoreConfig('indexa/installments/n_parcelas_s_juros');
    }
    
    public function getTaxaJuros() {
        return Mage::getStoreConfig('indexa/installments/taxa_juros');
    }
    
    public function setValue($value) {
        $this->_value = $value;
        return $this;
    }

    public function getValue() {
        if (!$this->_value) {
            if( Mage::getSingleton('checkout/session')->getBaseTotal() ){
                $this->_value = Mage::getSingleton('checkout/session')->getBaseTotal();
            }else{
                $this->_value = Mage::getSingleton('checkout/session')->getQuote()->getGrandTotal();
                Mage::getSingleton('checkout/session')->setBaseTotal( Mage::getSingleton('checkout/session')->getQuote()->getGrandTotal() );
            }
            
        }
        return $this->_value;
    }
    
     public function getInstallmentSequence() {
        $installments = Mage::getModel('installments/installmentset');

        if ($this->getValue() <= 0) {
            return $installments;
        }

        $juros = $this->getTaxaJuros() / 100;
        $capital = $this->getValue();
        $capitalJuros = $capital;

        $max = $this->getMaxParcelas();
        $min = $this->getParcelaMinima();

        for ($parcela = 1; $parcela <= $max; $parcela++) {
            
            if ($parcela == 1) {
                $installments->pushInstallment($capital, $this->getMessage(0));
            } else {
      
                //Define se a parcela será com ou sem juros
                if ($juros > 0 && ($parcela) > $this->getParcelaSemJuros()) {
                    $divided = ($capital) * ( ($juros) / ( 1 - (1 / pow( (1+$juros), $parcela ))));
                    $jurosMessage = $this->getTaxaJuros();
                   
                } else {
                    $divided = $capital / $parcela;
                    $jurosMessage = '0';
                  
                }

                if ($divided >= floatval($min)) {
                    $installments->pushInstallment($divided, $this->getMessage($jurosMessage), $parcela);
                }
            }
        }

        return $installments;
    }

    public function getInstallmentHighest() {
        $installments = $this->getInstallmentSequence();
        $last = false;

        foreach ($installments->returnIterable() as $installment) {
            $last = $installment;
        }

        return $last;
    }

    public function getInstallmentByItem( $parcela ) {
        $installments = $this->getInstallmentSequence();
        $last = false;

        foreach ($installments->returnIterable() as $installment) {
            $last = $installment;
            if( $installment->getValue() == $parcela ){
                break;
            }
        }

        return $last;
    }

}