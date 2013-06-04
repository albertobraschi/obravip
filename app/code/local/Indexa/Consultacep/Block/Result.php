<?php

class Indexa_Consultacep_Block_Result extends Mage_Core_Block_Template {

    protected function _construct() {
        parent::_construct();
    }

    public function getCepResults() {


        $tx_cep = $this->getRequest()->getPost(Indexa_Consultacep_Model_Consultacep::CEP_FIELD);
        $tx_cidade = $this->getRequest()->getPost(Indexa_Consultacep_Model_Consultacep::CIDADE_FIELD);
        $tx_bairro = $this->getRequest()->getPost(Indexa_Consultacep_Model_Consultacep::BAIRRO_FIELD);

        $tx_cep = preg_replace("/[^0-9]/", "", $tx_cep);
        $tx_bairro = preg_replace("/[^A-Za-z0-9ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ]/", "", $tx_bairro);

        $collection = Mage::getModel('indexa_consultacep/consultacep')->getCollection();
        $collectionResult = array();

        $exploded = explode(',', $tx_cidade);


        // arrays com ids numéricos
        if (count($exploded) > 0) {
            for ($i = 0; $i < count($exploded); $i++) {
                $exploded[$i] = preg_replace("/[^0-9]/", "", $exploded[$i]);
                if (!is_numeric($exploded[$i])) {
                    unset($exploded[$i]);
                }
            }
        }

        // sem cidade
        $cidades_final = false;

        if (count($exploded) > 0) {
            // várias cidades
            $cidades_final = $exploded;
        } else if (strlen(trim($tx_cidade)) > 0 && is_numeric($tx_cidade)) {
            // uma cidade só
            $cidades_final = $tx_cidade;
        }

        if (strlen(trim($tx_cep)) == 8 and is_numeric($tx_cep)) {
            $collectionResult = $collection->getCEPinMatrixRates($tx_cep, $cidades_final);
            if (is_object($collectionResult)) {
                $collectionResult = $collectionResult->getData();
            }
        } else if (strlen(trim($tx_bairro)) > 0) {
            $collectionResult = $collection->getBairroinMatrixRates($tx_bairro, $cidades_final);
            if (is_object($collectionResult)) {
                $collectionResult = $collectionResult->getData();
            }
        } else {
            return $this->__('Favor digitar um valor válido para a busca.');
        }

         /**
         * @todo Refatorar para um método separado.
         */
        // grava cep na sessão
        Mage::getSingleton('core/session')->setFrontendCep($tx_cep);

        // verifica se é válido
        if (is_array($collectionResult) && sizeof($collectionResult) > 0 && is_numeric($collectionResult[0]['cep_valido'])) {
            Mage::getSingleton('core/session')->setFrontendCepIsValid(true);
        } else {
            Mage::getSingleton('core/session')->setFrontendCepIsValid(false);
        }

        return $collectionResult;
    }

    public function formaDeliveryType($cep) {
        $result = Mage::getModel('indexa_consultacep/consultacep')->formaDeliveryType($cep);

        return $result;
    }

}