<?php

/**
 * Magento Indexa Shipping Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @title      Magento -> Indexa Shipping Module
 * @category   Indexa
 * @package    Indexa_Matrixrate
 * @copyright  Copyright (c) 2010 Indexa Internet (http://www.indexainternet.com.br)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Indexa Development Team <desenvolvimento@indexainternet.com.br> / Karen Baker <enquiries@auctionmaid.com>
 */
class Indexa_Matrixrate_Model_Carrier_Matrixrate extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'matrixrate';
    protected $_default_condition_name = 'package_weight';
    protected $_conditionNames = array();

    public function __construct() {
        parent::__construct();
        foreach ($this->getCode('condition_name') as $k => $v) {
            $this->_conditionNames[] = $k;
        }
    }

    /**
     * 
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $freeBoxes = 0;
        $found = false;
        $virtualTotal = 0;
        $OutOfStock = false;
        foreach ($request->getAllItems() as $item) {
        	
        	//Quantidade do produto em estoque.
        	$stockQty = $item->getProduct()->getStockItem()->getQty();
        	//Verificar se quantidade solicitada é maior que a quantidade em estoque.
        	if ($item->getQty() > $stockQty) {
        		$OutOfStock = true;
        	}
        	 
        	$_product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
        	
            if ($item->getFreeShipping() && $item->getProductType() != Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL
                    && $item->getProductType() != 'downloadable') {
                $freeBoxes+=$item->getQty();
            }
            if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL ||
                    $item->getProductType() == 'downloadable') {
                $virtualTotal+= $item->getBaseRowTotal();
                $found = true;
            }
        }
        if ($found && $this->getConfigFlag('remove_virtual')) {
            $request->setPackageValue($request->getPackageValue() - $virtualTotal);
        }
        $this->setFreeBoxes($freeBoxes);

        $request->setConditionName($this->getConfigData('condition_name') ? $this->getConfigData('condition_name') : $this->_default_condition_name);

        $result = Mage::getModel('shipping/rate_result');

        $array_peso_cubico = $this->getCartWeight();
        $request->setData("package_weight", $array_peso_cubico["pesocubico"]);

        if (!$request->getPackageWeight()) {
            $request->setPackageWeight(-1);  /*if error to make impossible to find shipping rate*/
        }
        
        $ratearray = $this->getRate($request);

        if (empty($ratearray)) {

            $error = Mage::getModel('shipping/rate_result_error')
                    ->setCarrier('matrixrate')
                    ->setErrorMessage($this->getConfigData('specificerrmsg'))
                    ->setCarrierTitle($this->getConfigData('title'));
            return $error;
        }

        $freeShipping = false;

        if ($this->getConfigData('enable_free_shipping_threshold') &&
                $this->getConfigData('free_shipping_threshold') != '' &&
                $this->getConfigData('free_shipping_threshold') > 0 &&
                $request->getPackageValue() > $this->getConfigData('free_shipping_threshold')) {
            $freeShipping = true;
        }
        if ($this->getConfigData('allow_free_shipping_promotions') &&
                ($request->getFreeShipping() === true ||
                $request->getPackageQty() == $this->getFreeBoxes())) {
            $freeShipping = true;
        }

        if ($freeShipping) {
            $method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier('matrixrate');
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethod('matrixrate_free');
            $method->setPrice('0.00');
            $method->setMethodTitle($this->getConfigData('free_method_text'));
            $result->append($method);

            if ($this->getConfigData('show_only_free')) {
                return $result;
            }
        }

        foreach ($ratearray as $rate) {
            if (!empty($rate) && $rate['price'] >= 0) {

                $method = Mage::getModel('shipping/rate_result_method');

                $method->setCarrier('matrixrate');
                $method->setCarrierTitle($this->getConfigData('title'));

                $method->setMethod('matrixrate_'. $rate['pk']);

                
                if ( $OutOfStock && is_numeric($_product->getPrazoEncomenda()) ) 
                {
                	$prazo_encomenda = $rate['estimated_delivery_time'] + $_product->getPrazoEncomenda();
                	$method_title = $rate['delivery_type'].' | '.Mage::helper('matrixrate')->__('Up to %s working days', $prazo_encomenda);
                } 
                else 
                {
                	$method_title = $rate['delivery_type'].' | '.Mage::helper('matrixrate')->__('Up to %s working days', $rate['estimated_delivery_time']);
                }                

                $method->setMethodTitle($method_title);
                
                $shippingPrice = $this->getFinalPriceWithHandlingFee($rate['price']);
                $method->setCost($rate['cost']);
                $method->setDeliveryType($rate['delivery_type']);

                $method->setPrice($shippingPrice);

                $result->append($method);
            }
        }

        return $result;
    }

    public function getRate(Mage_Shipping_Model_Rate_Request $request) {
        return Mage::getResourceModel('matrixrate_shipping/carrier_matrixrate')->getNewRate($request, $this->getConfigFlag('zip_range'));
    }

    public function getCode($type, $code='') {
        $codes = array(
            'condition_name' => array(
                'package_weight' => Mage::helper('matrixrate')->__('Weight vs. Destination'),
                'package_value' => Mage::helper('matrixrate')->__('Price vs. Destination'),
                'package_qty' => Mage::helper('matrixrate')->__('# of Items vs. Destination'),
            ),
            'condition_name_short' => array(
                'package_weight' => Mage::helper('matrixrate')->__('Weight (and above)'),
                'package_value' => Mage::helper('matrixrate')->__('Order Subtotal (and above)'),
                'package_qty' => Mage::helper('matrixrate')->__('# of Items (and above)'),
            ),
        );

        if (!isset($codes[$type])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('matrixrate')->__('Invalid Matrix Rate code type: %s', $type));
        }

        if ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw Mage::exception('Mage_Shipping', Mage::helper('matrixrate')->__('Invalid Matrix Rate code for type %s: %s', $type, $code));
        }

        return $codes[$type][$code];
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods() {
        return array('matrixrate' => $this->getConfigData('name'));
    }

    /**
     * Calcula o peso e cúbico dos itens do carrinho.
     * Retorna o maior, o menor, e a raiz cúbica ajustada
     * aos valores mínimos
     *
     * @return mixed
     */
    public function getCartWeight() {
        $items = Mage::getSingleton('checkout/cart')->getItems();
        $cProduct = Mage::getModel('catalog/product');

        $cubeEach = 0.0;

        foreach ($items as $item) {
            $cProduct->load($item->getProduct()->getId());

            if ($cProduct->getRespeita_cubagem()) {
                $cubeEach += (($cProduct->getAltura() * $cProduct->getComprimento() * $cProduct->getLargura()) / 4800) * $item->getQty();
            } else {
                $cubeEach += $cProduct->getWeight() * $item->getQty();
            }
        }

        $arr = array(
            "pesocubico" => $cubeEach,
        );

        return $arr;
    }

}

