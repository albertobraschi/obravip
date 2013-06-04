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
class Indexa_Mc_Model_Log extends Mage_Core_Model_Abstract {
    //Contantes utilizadas
    const CONF_LOG_PATH = '/tmp/log/';

    //Variáveis utilizadas
    protected $grava_log = 1;

    /**
     * Constructor Method
     */
    protected function _construct() {
        $this->grava_log = (int) Mage::getStoreConfig('indexa/indexa_mc/grava_log');
        $this->_init('indexa_mc/log');
    }

    /**
     * Register the log string in the database
     *
     * @param integer $order
     * @param string $robot
     * @param string $method
     * @param string $status
     * @param string $message
     * @param string $messageGateway
     * @return Indexa_Mc_Model_Log
     */
    public function add($order, $robot, $method, $status, $message, $messageGateway = null) {
        $this->setOrderId($order)
                ->setRobot($robot)
                ->setMethod($method)
                ->setStatus($status)
                ->setMessage($message)
                ->setMessageGateway($messageGateway)
                ->setDatetime(date('Y-m-d H:i:s'))
                ->save();
        return $this->setId(null);
    }

    /**
     * Ovewrite default save method
     */
    public function save() {
        //Se a flag de gravação de log está ativada
        if ($this->grava_log) {
            try {
                parent::save();
            } catch (Exception $e) {
                $mensagem = '(' . date('Y-m-d H:i:s') . ') ' . PHP_EOL . serialize($this->getData()) . PHP_EOL . PHP_EOL;
                Mage::log( $mensagem, Zend_Log::DEBUG );
            }
        }
    }

}
