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
class Indexa_Mc_Model_Mc extends Mage_Core_Model_Abstract {
    //Constantes utilizadas

    const STATUS_AUTHORIZED = 'authorized';
    const STATUS_CAPTURED = 'captured';
    const STATUS_CAPTUREPAYMENT = 'capture payment';
    const STATUS_CREATED = 'created';
    const STATUS_DENIED = 'denied';
    const STATUS_ERROR = 'error';
    const STATUS_FINISHED = 'finished';
    const STATUS_MAXTRIES = 'max tries';
    const STATUS_PROCESSING = 'processing';

    //Variáveis utilizadas
    private $grava_log = 1;
    private $qtd_tentativas = 3;
    
    private $rc_list = array();

    /**
     * Define initial data from config table
     *
     * @return boolean
     */
    private function defineConfig() {
        $log = Mage::getModel('indexa_mc/log');        
        try {
            //Define os dados
            $this->grava_log = (Mage::getStoreConfig('indexa/indexa_mc/grava_log')) ? Mage::getStoreConfig('indexa/indexa_mc/grava_log') : $this->grava_log;
            $this->qtd_tentativas = (Mage::getStoreConfig('indexa/indexa_mc/qtd_tentativas')) ? Mage::getStoreConfig('indexa/indexa_mc/qtd_tentativas') : $this->qtd_tentativas;
            return true;
        } catch (Exception $e) {
            //Salva log
            $log->add(null, '+ Conversao', 'defineConfig()', self::STATUS_ERROR, 'Ocorreu um erro', serialize($e->getMessage()));
        }
    }

    /**
     * Creates a lock to a specified resource
     *
     * @param string $model
     * @param string $id
     * @return Varien_Object
     */
    public function criaLock($model, $id) {
        $log = Mage::getModel('indexa_mc/log');
        try {
            //Obtém e bloqueia o recurso
            $locker = Mage::getModel($model);
            $locker->_id = $id;
            //Se o recurso já estiver alocado
            if ($locker->isLocked()) {
                //Cancela a execução
                $log->add(null, '+ Conversao', 'criaLock()', self::STATUS_ERROR, 'Robo ja em execucao', serialize($e));
                exit;
            }
            //Aloca o recurso
            $locker->lock();
            //Retorna o recurso
            return $locker;
        } catch (Exception $e) {
            //Salva log
            $log->add(null, '+ Conversao', 'criaLock()', self::STATUS_ERROR, 'Ocorreu um erro', serialize($e));
        }
    }

    /**
     * Credit Card Authorizantion Robot
     *
     * @return boolean
     */
    public function authorize() {
        
        if( Mage::getStoreConfig('payment/gwap_cc/captura' ) ){
            return true;
        }
        
        $time = "";
        //Obtém e bloqueia o recurso
        $locker = $this->criaLock('indexa_mc/locker', 'payment_authorize');
        //Define as configurações
        $this->defineConfig();
        $log = Mage::getModel('indexa_mc/log');

        //Registra uma variável no Magento
        if (!Mage::registry('authorizeRobot'))
            Mage::register('authorizeRobot', true);

        //Carrega todos os pedidos
        $gatewayPayments = Mage::getModel('indexa_mc/payment')->getCollection()
                                ->addTimeFilter(Mage::getStoreConfig('indexa/indexa_mc/tempo_espera'))
                                ->addTypeFilter('cc')
                                ->addStatusFilter(self::STATUS_CREATED);
        
        //Percorre todos os pedidos criados
        foreach ($gatewayPayments as $gatewayPayment) {

            //Se o número de tentativas for menor que o máximo
            if ($gatewayPayment->getTries() < $this->qtd_tentativas && $gatewayPayment->getStatus() != self::STATUS_MAXTRIES) {

                //Pega os dados do Pedido
                $order = Mage::getModel('sales/order')->load($gatewayPayment->getOrderId());
                /* @var $order Mage_Sales_Model_Order */
                if( $order->getState() == Mage_Sales_Model_Order::STATE_CANCELED ){
                    $gatewayPayment->setInfo(null);
                    $gatewayPayment->setStatus(Mage_Sales_Model_Order::STATE_CANCELED);
                    $gatewayPayment->setAbandoned(1);
                    $gatewayPayment->setUpdatedAt(date("Y-m-d H:i:s"));
                
                    $gatewayPayment->save();
                    
                    continue;
                }
                try {
                    //Chama o método Authorize do Magento
                    //die( get_class($order->getPayment()->getMethodInstance()) );
                    $retorno = $order->getPayment()->getMethodInstance()->authorize($order->getPayment(), $order->getGrandTotal());
                    //Atualiza o objeto, para caso tenha sido atualizado no método Authorize
                    $gatewayPayment = Mage::getModel('indexa_mc/payment')->load($gatewayPayment->getId());
                    //Salva log
                    if( !Mage::getStoreConfig('payment/'.$order->getPayment()->getMethod().'/acao') ){
                        $log->add($gatewayPayment->getOrderId(), 'Payment', 'authorize()', self::STATUS_AUTHORIZED, 'Pagamento autorizado');
                    }
                    
                    //Altera os dados na tabela auxiliar
                    $gatewayPayment->setStatus(((Mage::getStoreConfig('indexa/fcontrol/active') && Mage::getStoreConfig('payment/'.$order->getPayment()->getMethod().'/mc_fraud_check') ) ? self::STATUS_AUTHORIZED : self::STATUS_CAPTUREPAYMENT));
                    $gatewayPayment->setErrorCode(null);
                    $gatewayPayment->setErrorMessage(null);
                    $time = Mage::getStoreConfig('indexa/indexa_mc/tempo_espera');
                    $gatewayPayment->setUpdatedAt( date("Y-m-d H:i:s", strtotime("-{$time} hours")) );
                    $gatewayPayment->setTries(0);
                    $gatewayPayment->setAbandoned(0);
                } catch (Exception $e) {
                    //Salva log
                    $log->add($gatewayPayment->getOrderId(), '+ Conversao', 'authorize()', self::STATUS_ERROR, 'Ocorreu um erro', $e->getMessage());
                    $gatewayPayment->setUpdatedAt(date("Y-m-d H:i:s"));
                }
               //Incrementa o número de tentativas
                if( Mage::getStoreConfig('payment/' . $order->getPayment()->getMethod() . '/mc_tries' ) ){
                    $gatewayPayment->setTries($gatewayPayment->getTries() + 1);
                }
            }

            //Se atingiu o número máximo de tentativas
            else {
                //Salva log
                $log->add($gatewayPayment->getOrderId(), '+ Conversao', 'authorize()', self::STATUS_MAXTRIES, 'Número máximo de tentativas atingido');
                //Define os dados da tabela auxiliar
                $gatewayPayment->setInfo(null);
                $gatewayPayment->setStatus(self::STATUS_MAXTRIES);
                $gatewayPayment->setAbandoned(1);
                $gatewayPayment->setUpdatedAt(date("Y-m-d H:i:s"));
                //Muda o status do pedido
                $order = Mage::getModel('sales/order')->load($gatewayPayment->getOrderId())->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
            }

            //Salva as alterações
            $gatewayPayment->save();
        }

        //Desaloca o recurso
        $locker->unlock();
        //Retorno padrão
        return true;
    }

    public function captureCc(){
        if( Mage::getStoreConfig('payment/gwap_cc/captura' ) )
            return true;
        else
            return $this->capture('cc');
    }
    
    public function captureBoleto(){
        $url = '';
       
        $config = mage::helper('gwap')->getConfig();
        $auth = mage::helper('gwap')->getAuthConfig();
        
        if ($config->getAmbiente() == 'LIVE') {
            $url = "https://ctpe.io/payment/query";
        } elseif ($config->getAmbiente() == 'CONNECTOR_TEST') {
            $url = "https://test.ctpe.io/payment/query";
        }            
        //capture itau
        $transaction_type_itau = trim($config->getData('transaction_channel_itau'));
        
        $cancelamento = $config->getCancelamento() ? $config->getCancelamento() : ($config->getVencimento() ? $config->getVencimento() : '5');
            
        $cancelamento_time = Mage::getModel('core/date')->timestamp( "-{$cancelamento} days" );
        $now_time = Mage::getModel('core/date')->timestamp(time());

        $date_now = date('Y-m-d', $now_time);
        $date_cancelamento = date('Y-m-d', $cancelamento_time);
        
        if( $transaction_type_itau ){
            $captureParams  = '<Request version="1.0">';
            $captureParams .= '<Header>';
            $captureParams .= '<Security sender="' . $auth->getSecuritySender() . '"/>';
            $captureParams .= '</Header>';
            $captureParams .= '<Query mode="'.($config->getAmbiente()).'" level="CHANNEL" entity="'.$transaction_type_itau.'" type="STANDARD">';
            $captureParams .= '<User login="' . ($auth->getUserLogin()) . '" pwd="' . (strval(Mage::helper("core")->decrypt($auth->getUserPwd()))) . '"/>';
            $captureParams .= '<Period from="'.$date_cancelamento.'" to="'.$date_now.'"/>';
            $captureParams .= '<Types>';
            $captureParams .= '<Type code="RC"/>';
            $captureParams .= '</Types>';
            $captureParams .= '</Query>';
            $captureParams .= '</Request>';
           
            $ch = curl_init($url);
            #curl_setopt($ch, CURLOPT_MUTE, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/x-www-form-urlencoded;charset=UTF-8"
    ));
            curl_setopt($ch, CURLOPT_POSTFIELDS, "load=$captureParams");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $curlresultURL = curl_exec($ch);
            curl_close($ch);

            $result = new Varien_Simplexml_Config($curlresultURL);
             if( $result->getNode('Result') && $result->getNode('Result')->getAttribute('count')  > 0 ){
                  $resultCode =  $result->getNode('Result/Transaction');
                  foreach ($resultCode as $rc){
                        $id = $rc->children()->descend('Identification')->children()->TransactionID->__toString(); 
                        $code = $rc->children()->descend('Processing')->getAttribute('code');
                        $resultCode = explode('.', $code);

                        // validate Pre authorization - 90 success code
                        if ($resultCode[2] != '90') {
                           continue;
                        }
                        
                        $this->rc_list[] =  $id;
                  }

            }   
        }
        
        //capture bradesco
        $transaction_type_bradesco = trim($config->getData('transaction_channel_bradesco'));
        if( $transaction_type_bradesco ){
            $captureParams  = '<Request version="1.0">';
            $captureParams .= '<Header>';
            $captureParams .= '<Security sender="' . $auth->getSecuritySender() . '"/>';
            $captureParams .= '</Header>';
            $captureParams .= '<Query mode="'.($config->getAmbiente()).'" level="CHANNEL" entity="'.$transaction_type_bradesco.'" type="STANDARD">';
            $captureParams .= '<User login="' . ($auth->getUserLogin()) . '" pwd="' . (strval(Mage::helper("core")->decrypt($auth->getUserPwd()))) . '"/>';
            $captureParams .= '<Period from="'.$date_cancelamento.'" to="'.$date_now.'"/>';
            $captureParams .= '<Types>';
            $captureParams .= '<Type code="RC"/>';
            $captureParams .= '</Types>';
            $captureParams .= '</Query>';
            $captureParams .= '</Request>';
           
            $ch = curl_init($url);
            #curl_setopt($ch, CURLOPT_MUTE, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/x-www-form-urlencoded;charset=UTF-8"
    ));
            curl_setopt($ch, CURLOPT_POSTFIELDS, "load=$captureParams");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $curlresultURL = curl_exec($ch);
            curl_close($ch);

            $result = new Varien_Simplexml_Config($curlresultURL);
             if( $result->getNode('Result') && $result->getNode('Result')->getAttribute('count')  > 0 ){
                  $resultCode =  $result->getNode('Result/Transaction');
                  foreach ($resultCode as $rc){
                      $this->rc_list[] =  $rc->children()->descend('Processing')->getAttribute('code');
                  }
            }   
        }
        
        return $this->capture('boleto');
    }
    
    
    public function checkBoleto( $incrementId ){
        if( !in_array($incrementId, $this->rc_list) ){
           
            return Mage::throwException(Mage::helper('gwap')->__('Pagamento não efetuado'));  
        }
        return true;
    }
    
    public function capture( $type ){
        //Obtém e bloqueia o recurso
        
        $locker = $this->criaLock('indexa_mc/locker', 'payment_capture');
        //Define as configurações
        
        $this->defineConfig();
        $log = Mage::getModel('indexa_mc/log');


        //Registra uma variável no Magento
        if (!Mage::registry('captureRobot'))
            Mage::register('captureRobot', true);

        //Se o módulo do Fcontrol estiver ativo
        if (Mage::getStoreConfig('indexa/fcontrol/active')) {
            //Carrega todos os pedidos capturados do Fcontrol
            $fcontrolOrders = Mage::getModel('fcontrol/orders')->getCollection()->addStatusFilter(self::STATUS_CAPTUREPAYMENT);
            //Percorre todas as orders
            foreach ($fcontrolOrders as $fcontrolOrder) {
                //Atualiza os dados
                $fcontrolOrder->setStatus(self::STATUS_FINISHED);
                $fcontrolOrder->save();
                //Atualiza os dados da tabela auxiliar
                $gatewayPayment = Mage::getModel('indexa_mc/payment')->load($fcontrolOrder->getOrderId(), 'order_id');
                $gatewayPayment->setStatus(self::STATUS_CAPTUREPAYMENT);
                $gatewayPayment->setTries(0);
                $gatewayPayment->save();
            }
        }
        
        //Carrega todos os pedidos
        $gatewayPayments = Mage::getModel('indexa_mc/payment')->getCollection()
                                ->addTimeFilter(Mage::getStoreConfig('indexa/indexa_mc/tempo_espera'))
                                ->addTypeFilter($type)
                                ->addStatusFilter(self::STATUS_CAPTUREPAYMENT);
        
        //Percorre todos os pedidos criados
        foreach ($gatewayPayments as $gatewayPayment) {

        
            //Se o número de tentativas for menor que o máximo
            if ($gatewayPayment->getTries() < $this->qtd_tentativas && $gatewayPayment->getStatus() != self::STATUS_MAXTRIES) {

                //Pega os dados do Pedido
                $order = Mage::getModel('sales/order')->load($gatewayPayment->getOrderId());
                /* @var $order Mage_Sales_Model_Order */
                if( $order->getState() == Mage_Sales_Model_Order::STATE_CANCELED ){
                    $gatewayPayment->setInfo(null);
                    $gatewayPayment->setStatus(Mage_Sales_Model_Order::STATE_CANCELED);
                    $gatewayPayment->setAbandoned(1);
                    $gatewayPayment->setUpdatedAt(date("Y-m-d H:i:s"));
                
                    $gatewayPayment->save();
                    
                    continue;
                }
                try {
                    if( $type == 'boleto' ){
                        //Chama o método Capture do Magento
                        $retorno = $this->checkBoleto( $order->getIncrementId() );                        
                    }else{
                        $retorno = $order->getPayment()->getMethodInstance()->capture($order->getPayment(), $order->getGrandTotal());
                    }
                    
                    //Atualiza o objeto, para caso tenha sido atualizado no método Capture
                    $gatewayPayment = Mage::getModel('indexa_mc/payment')->load($gatewayPayment->getId());
                    //Salva log
                    $log->add($gatewayPayment->getOrderId(), 'Payment', 'capture()', self::STATUS_CAPTURED, 'Pagamento capturado');
                    //Altera os dados na tabela auxiliar
                    $gatewayPayment->setStatus(self::STATUS_CAPTURED);
                    $gatewayPayment->setErrorCode(null);
                    $gatewayPayment->setErrorMessage(null);
                    $gatewayPayment->setInfo(null);
                    $gatewayPayment->setTries(0);
                    $gatewayPayment->setAbandoned(0);
                    //Gera invoice e manda e-mail
                    $invoice = $order->prepareInvoice()->register();
                    $invoice->setEmailSent(false);
                    $invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_PAID);
                    $invoice->getOrder()->setTotalPaid($order->getGrandTotal());
                    $invoice->getOrder()->setBaseTotalPaid($order->getBaseGrandTotal());
                    $invoice->getOrder()->setCustomerNoteNotify(true);
                    $invoice->getOrder()->setIsInProcess(true);
                    Mage::getModel('core/resource_transaction')->addObject($invoice)->addObject($invoice->getOrder())->save();
                    $invoice->sendEmail(true, 'Pedido realizado com sucesso');
                    //Altera o status da order
                    $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
                } catch (Exception $e) {
                    //Salva log
                    
                    $log->add($gatewayPayment->getOrderId(), '+ Conversao', 'capture()', self::STATUS_ERROR, 'Ocorreu um erro', $e->getMessage());
                }

                //Incrementa o número de tentativas
                if( Mage::getStoreConfig('payment/' . $order->getPayment()->getMethod() . '/mc_tries' ) ){
                    $gatewayPayment->setTries($gatewayPayment->getTries() + 1);
                }

                //Salva as alterações
                $gatewayPayment->setUpdatedAt(date("Y-m-d H:i:s"));
                $gatewayPayment->save();
            }

            //Se atingiu o número máximo de tentativas
            else {
                //Salva log
                $log->add($gatewayPayment->getOrderId(), '+ Conversao', 'capture()', self::STATUS_MAXTRIES, 'Número máximo de tentativas atingido');
                //Define os dados da tabela auxiliar
                $gatewayPayment->setInfo(null);
                $gatewayPayment->setStatus(self::STATUS_MAXTRIES);
                $gatewayPayment->setAbandoned(1);
                $gatewayPayment->setUpdatedAt(date("Y-m-d H:i:s"));
                $gatewayPayment->save();
                //Muda o status do pedido
                $order = Mage::getModel('sales/order')->load($gatewayPayment->getOrderId())->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
            }
        }

        //Desaloca o recurso
        $locker->unlock();
        //Retorno padrão
        return true;
    }

    public function abandonedCart() {
        $collection = Mage::getResourceModel('reports/quote_collection');
        $collection->prepareForAbandonedReport(array(1));
        if ($collection->load()) {
            foreach ($collection->load() as $quote) {
                $dataAtual = strtotime(date('Y-m-d H:i:s'));
                $dataLimpeza = new DateTime($quote->getPayment()->getUpdatedAt());
                $dataLimpeza->modify('+30 minutes');
                $dataLimpeza = strtotime($dataLimpeza->format('Y-m-d H:i:s'));
                if ($dataAtual >= $dataLimpeza)
                    $quote->getPayment()->delete();
            }
        }
    }

}