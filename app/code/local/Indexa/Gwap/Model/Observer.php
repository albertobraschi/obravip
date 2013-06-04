<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Indexa_Gwap_Model_Observer
{
     /**
     * Set forced canCreditmemo flag
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Payment_Model_Observer
     */
    public function salesOrderSave($observer)
    {
        $order = $observer->getOrder();
        $quote = $observer->getQuote();
        
        $payment = $order->getPayment();
        
        if( !Mage::getStoreConfig('payment/'.$payment->getMethod().'/mc_active') ){
            return $this;
        }
        
        $data = new Varien_Object();
        
        if($payment->getCcType())
            $data->setCcType($payment->getCcType());
        if($payment->getCcOwner())
            $data->setCcOwner($payment->getCcOwner());
        if($payment->getCcLast4())
            $data->setCcLast4($payment->getCcLast4());
        if($payment->getCcNumber())
            $data->setCcNumber($payment->getCcNumber());
        if($payment->getCcParcelas())
            $data->setCcParcelas($payment->getCcParcelas());
        if($payment->getCcCid())
            $data->setCcCid($payment->getCcCid());
        if($payment->getCcExpMonth())
            $data->setCcExpMonth($payment->getCcExpMonth());
        if($payment->getCcExpYear())
            $data->setCcExpYear($payment->getCcExpYear());
        if($payment->getGwapBoletoType())
            $data->setGwapBoletoType($payment->getGwapBoletoType());
        
         /**
         * create braspag payment
         */
        $mGwap = Mage::getModel('gwap/order');
        $mGwap->setStatus(Indexa_Gwap_Model_Order::STATUS_CREATED);
        $mGwap->setCreatedAt(date('Y-m-d H:i:s'));
        $mGwap->setInfo(Mage::helper('core')->encrypt(serialize($data->toArray())));
        $mGwap->setType(Mage::getStoreConfig('payment/'.$payment->getMethod().'/mc_type'));

        $mGwap->setOrderId($order->getId());
        $mGwap->save();
                
        return $this;
    }
    
     public function addBoletoLink($observer){
        $orderId = current($observer->getOrderIds());
        $mGwap = Mage::getModel('gwap/order')->load($orderId, 'order_id');
        if( $mGwap->getType() != 'boleto' || !Mage::getStoreConfig( 'payment/gwap_boleto/show_link' ) ){
            return $this;
        }
        
        $order =  Mage::getModel('sales/order')->load($orderId);
        $customerId = $order->getCustomerId();
        
        $storage = Mage::getSingleton('checkout/session');
        
        if ($storage) {
            $block = Mage::app()->getLayout()->getMessagesBlock();
            
            $url = Mage::helper('gwap')->getBoletoUrl( $order->getIncrementId() );
            
            $linkMessage = Mage::helper('gwap')->__('Clique aqui');
            $block->addSuccess( 
                sprintf( Mage::helper('gwap')->__( '%s para imprimir seu boleto.' ), '<a href="'.$url.'" target="_blank" class="imprimir_boleto">'.$linkMessage.'</a>' ) 
            );
            $block->setEscapeMessageFlag(false);            
        }
    }
    
    /**
     * cancela verificacoes de boletos e pedidos de acordo com o vencimento
     * 
     * @return Indexa_Gwap_Model_Observer 
     */
    public function cancelBoleto(){
        $cancelamento = Mage::getStoreConfig( 'payment/gwap_boleto/cancelamento' );
        if( is_numeric($cancelamento) && $cancelamento > 0 ){    
            $cancelamento++;
            $due_date = Mage::getModel('core/date')->timestamp( '-'.$cancelamento.' days' );
        }else{
            $due_date = Mage::getModel('core/date')->timestamp( '-2 days' );
        }
        
        $mGwap = Mage::getModel('gwap/order')->getCollection()
                ->addExpireFilter( $due_date )
                ->addTypeFilter('boleto')
                ->addStatusFilter(Indexa_Gwap_Model_Order::STATUS_CAPTUREPAYMENT);
      
        if( $mGwap->count() ){
           foreach ($mGwap as $mGwapitem){
                
                $mGwapitem->setStatus('canceled');
                $mGwapitem->save();
                
                $can_cancel = Mage::getStoreConfig( 'payment/gwap_boleto/cancelar_expirado' );
                
                if( $can_cancel ){
                    
                    $order = Mage::getModel('sales/order')->load( $mGwapitem->getOrderId() );
                    /* var $order Mage_Sales_Model_Order */
                    $order->cancel();
                    $order->save();
                
                }
                
            }
        }
        return $this;
    }
}
