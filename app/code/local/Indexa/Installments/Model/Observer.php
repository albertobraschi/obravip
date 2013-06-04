<?php

class Indexa_Installments_Model_Observer {
    const INDEXA_GWAP_CC_BLOCK = 'Indexa_Gwap_Block_Form_Cc';
    const MAGE_PRODUCT_PRICE = 'Mage_Catalog_Block_Product_Price';
    const CATEGORY_CONTROLLER = 'category';
    const PRODUCT_CONTROLLER = 'product';
    const INSTALLMENT_CODE = 'cc_parcelas';

    private $_installmentModel;

    /**
     * add installments block on form
     */
    public function installmentsPaymentBlock($observer) {
        if (!$this->isActive()) {
            return false;
        }
        if (self::INDEXA_GWAP_CC_BLOCK != get_class($observer->getBlock())) {
            return $this;
        }
        
        if( !Mage::getStoreConfig('indexa/installments/active_form') )  {
            return $this;
        }
        $block = $observer->getBlock()->getLayout()->createBlock('installments/form');
        
        $hasOnestep = Mage::getStoreConfig('onestepcheckout/general/rewrite_checkout_links');
        
        if ($hasOnestep) {
            $this->clearInfo();
            
            $checkout = Mage::getSingleton('checkout/session');           
            $checkout->getQuote()->collectTotals();
            $checkout->getQuote()->setTotalsCollectedFlag(false);
            
        }
        $html = str_replace('</ul>', '', $observer->getTransport()->getHtml());

        $observer->getTransport()->setHtml($html
                . $block
                        ->setPaymentFormBlock($observer->getBlock())
                        ->setPaymentInstallmentCode(self::INSTALLMENT_CODE)
                        ->setPaymentCode($observer->getBlock()->getMethodCode())
                        ->toHtml() . '</ul>'
        );

        return $this;
    }

    /**
     * add installments block on views
     */
    public function installmentsProductPrice($observer) {

        if (!$this->isActive()) {
            return false;
        }
        if (self::MAGE_PRODUCT_PRICE != get_class($observer->getBlock())) {
            return $this;
        }

        $controller_name = Mage::app()->getRequest()->getControllerName();

        if (Mage::registry('has_' . $controller_name)) {
            return $this;
        }
        if (self::PRODUCT_CONTROLLER == $controller_name) {
            Mage::register('has_' . $controller_name, true);
        }

        $render_block = $this->viewRender($controller_name);
        $product_price = $observer->getBlock()->getProduct()->getFinalPrice();
        
        if (!$render_block || !$product_price) {
            return $this;
        }

        $html = $observer->getTransport()->getHtml();
        
        $installments_html = $observer->getBlock()->getLayout()->createBlock('installments/' . $render_block)
                ->setValue($product_price)
                ->toHtml();

        $observer->getTransport()->setHtml($html . $installments_html);

        return $this;
    }

    /**
     * clear installments values from session after save shipping over onepage checkout
     */
    public function installmentsCheckoutSaveShipping($observer) {
        if (!$this->isActive()) {
            return $this;
        }
        $this->clearInfo();
    }

    public function installmentsCheckoutClearPayment($observer){
        if( Mage::getSingleton('checkout/session')->getQuote()->getPayment()->getMethod() != 'gwap_cc' )
            return $this;
        Mage::getSingleton('checkout/session')->getQuote()->getPayment()->setCcParcelas('');        
        return $this;
    }

    /**
     * calculate installments for gwap_cc method over onestepcheckout
     */
    public function installmentsCheckout($observer) {
        if (!$this->isActive()) {
            return $this;
        }

        $controller = $observer->getControllerAction();
        $checkout = Mage::getSingleton('checkout/session');
        $params = $controller->getRequest()->getParams();
        
        
        if ( !isset( $params['payment'] ) || ( $params['payment'] && ( ! isset($params['payment']['gwap_cc_cc_parcelas']) || $params['payment']['method'] != 'gwap_cc' ) ) ) {
            return $this;
        } 

        if( $checkout->getQuote()->getPayment()->getMethod() == 'gwap_cc' ){
            $this->clearInfo();
            $checkout->getQuote()->collectTotals();
            $checkout->getQuote()->setTotalsCollectedFlag(false);
        }

        $installment = $this->getInstallmentsModel()->getInstallmentByItem($params['payment']['gwap_cc_cc_parcelas']);
        $checkout->setJuros(( $installment->getInstallmentValue() * $installment->getValue() ) - $checkout->getBaseTotal());
    }

    
    /**
     * return installment model instance
     * 
     * @return type 
     */
    public function getInstallmentsModel() {
        if (!$this->_installmentModel) {
            $this->_installmentModel = Mage::helper('installments')->getInstallmentModel();
        }
        return $this->_installmentModel;
    }

    /**
     * check if module is active on admin
     * @return int
     */
    public function isActive() {
        return Mage::getStoreConfig('indexa/installments/active');
    }

    /**
     * returns a controller to render
     * 
     * @param string $controller_name
     * @return string 
     */
    public function viewRender($controller_name) { 
        return in_array( $controller_name, array('product', 'category') )  ? 
               Mage::getStoreConfig('indexa/installments/active_' . $controller_name):
               Mage::getStoreConfig('indexa/installments/active_general');
    }

    /**
     * clear installment information from session
     * 
     */
    public function clearInfo() {
        $checkout = Mage::getSingleton('checkout/session');

        $checkout->setJuros(0);
        $checkout->setBaseTotal(0);
    }
 
}