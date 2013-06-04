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

if (!defined("TIMEOUTRESPONSEVALUE")) {define("TIMEOUTRESPONSEVALUE", 25); }
if (!defined("TIMEOUTCONNECTIONVALUE")) {define("TIMEOUTCONNECTIONVALUE", 15); }

ini_set("allow_url_fopen", 1);
ini_set('default_socket_timeout', TIMEOUTRESPONSEVALUE);

class Indexa_Correios_Model_Carrier_Indexacorreios
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

	/**
	 * _code property
	 *
	 * @var string
	 */
	protected $_code = 'indexacorreios';
	
	/**
	 * _result property
	 *
	 * @var Mage_Shipping_Model_Rate_Result / Mage_Shipping_Model_Tracking_Result
	 */
	protected $_result = null;

	/**
	 * Check if current carrier offer support to tracking
	 *
	 * @return boolean true
	 */
	public function isTrackingAvailable() {
		return true;
	}

	
    /**
     * Collects the shipping rates from Correios.
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
    	
    	// check if this method is active
		if (!$this->getConfigFlag('active')) { return false; } // silent exit

		
		
		// INICIO OBTENCAO VALORES
		// ALGUNS COMENTARIOS MANTIDOS EM INGLES
		
		$result = Mage::getModel('shipping/rate_result');
		
		$error = Mage::getModel('shipping/rate_result_error')
			->setCarrier($this->_code)
			->setCarrierTitle($this->getConfigData('title'));
		
		// to check if this method is even applicable (MUST ship from Brazil)
		$origCountry = Mage::getStoreConfig('shipping/origin/country_id', $this->getStore());
		
		// to check if cart order value falls between the minimum and maximum order amounts required
		$packagevalue = $request->getBaseCurrency()
			->convert($request->getPackageValue(), $request->getPackageCurrency());
		$minorderval = $this->getConfigData('min_order_value');
		$maxorderval = $this->getConfigData('max_order_value');
		
		// postal codes
		$frompcode = preg_replace("/[^0-9]/","", Mage::getStoreConfig('shipping/origin/postcode', $this->getStore()) );
		$topcode = preg_replace("/[^0-9]/","", $request->getDestPostcode() );
		
		// destination country
		$destCountry = $request->getDestCountryId() ? $request->getDestCountryId() : "BR";

		// get weight units (kgs, grams)
		// o webservice do correio pede o peso em gramas
		// caso o usuario selecionou para colocar os pesos em kilos aqui multiplicaremos tudo por 1000
		// caso selecionou para colocar em gramas o valor sera multiplicado por 1
		$sweightunit = (float) $this->getConfigData('weight_units');
		$sweight = $request->getPackageWeight() / $sweightunit;
		
		$maopropria_fee = $this->getConfigData('maopropria_fee') == 1 ? "S" : "N";
		$ar_fee = $this->getConfigData('ar_fee') == 1 ? "S" : "N";
		$valordeclarado_fee = $this->getConfigData('valordeclarado_fee') == 1 ? true : false;
		
		$arr = $this->getCartWeight($sweight,$sweightunit);
		
		$array_errors = array(
			"0" => "Processamento com sucesso",
			"-1" => "Código de serviço inválido",
			"-2" => "CEP de origem inválido",
			"-3" => "CEP de destino inválido",
			"-4" => "Peso excedido",
			"-5" => "O Valor Declarado não deve exceder R$ 10.000,00",
			"-6" => "Serviço indisponível para o trecho informado",
			"-7" => "O Valor Declarado é obrigatório para este serviço",
			"-8" => "Este serviço não aceita Mão Própria",
			"-9" => "Este serviço não aceita Aviso de Recebimento",
			"-10" => "Precificação indisponível para o trecho informado",
			"-11" => "Para definição do preço deverão ser informados, também, o comprimento, a largura e altura do objeto em centímetros (cm).",
			"-12" => "Comprimento inválido.",
			"-13" => "Largura inválida.",
			"-14" => "Altura inválida.",
			"-15" => "O comprimento não pode ser maior que 60 cm.",
			"-16" => "A largura não pode ser maior que 60 cm.",
			"-17" => "A altura não pode ser maior que 60 cm.",
			"-18" => "A altura não pode ser inferior a 2 cm.",
			"-19" => "A altura não pode ser maior que o comprimento.",
			"-20" => "A largura não pode ser inferior a 5 cm.",
			"-21" => "A largura não pode ser menor que 11cm, quando o comprimento for menor que 25cm.",
			"-22" => "O comprimento não pode ser inferior a 16 cm.",
			"-23" => "A soma resultante do comprimento + largura + altura não deve superar a 150 cm.",
			"-24" => "Comprimento inválido.",
			"-25" => "Diâmetro inválido",
			"-26" => "Informe o comprimento.",
			"-27" => "Informe o diâmetro.",
			"-28" => "O comprimento não pode ser maior que 90 cm.",
			"-29" => "O diâmetro não pode ser maior que 90 cm.",
			"-30" => "O comprimento não pode ser inferior a 18 cm.",
			"-31" => "O diâmetro não pode ser inferior a 5 cm.",
			"-32" => "A soma resultante do comprimento + o dobro do diâmetro não deve superar a 104 cm.",
			"-33" => "Sistema temporariamente fora do ar. Favor tentar mais tarde.",
			"-34" => "Código Administrativo ou Senha inválidos.",
			"-35" => "Senha incorreta.",
			"-36" => "Cliente não possui contrato vigente com os Correios.",
			"-37" => "Cliente não possui serviço ativo em seu contrato.",
			"-38" => "Serviço indisponível para este código administrativo.",
			"-888" => "Erro ao calcular a tarifa",
			"7" => "Serviço indisponível, tente mais tarde",
			"99" => "Outros erros diversos do .Net"
		);
		
		// METODOS A SE VERIFICAR
		// 41106 PAC sem contrato
		// 40010 SEDEX sem contrato
		// 40045 SEDEX a Cobrar, sem contrato
		// 40215 SEDEX 10, sem contrato
		// 40290 SEDEX Hoje, sem contrato
		// 40096 SEDEX com contrato
		// 40436 SEDEX com contrato
		// 40444 SEDEX com contrato
		// 81019 e-SEDEX, com contrato
		// 41068 PAC com contrato
		
		$array_methods = array(
			"41106" => "PAC",
			"40010" => "SEDEX",
			"40045" => "SEDEX a Cobrar",
			"40215" => "SEDEX 10",
			"40290" => "SEDEX Hoje",
			"40096" => "SEDEX",
			"40436" => "SEDEX",
			"40444" => "SEDEX",
			"81019" => "e-SEDEX",
			"41068" => "PAC"
		);
		
		$shipping_methodst = explode(",", $this->getConfigData('postmethods'));
		$shipping_methodsc = explode(",", $this->getConfigData('postmethodsc'));
		
		// $shipping_methods = array("81019", "41106", "40010");
		$shipping_methods = array_merge($shipping_methodst, $shipping_methodsc);
		
		foreach ($shipping_methods as $key => $method)
		{
			if ( !array_key_exists($method, $array_methods) )
				unset($shipping_methods[$key]);
		}

		
		
		// INICIO VERIFICACOES DIVERSAS
		
		if ($origCountry != "BR") { return false; } // silent exit // @todo remover selecao de paises no admin?
		
		if (count($shipping_methods) <= 0) { return false; } // silent exit
		
		// valores do pedido dentro do permitido pelo admin 
    	if ($packagevalue < $minorderval || $packagevalue >= $maxorderval) { return false; } // silent exit
    	
		if(!preg_match('/^[0-9]{8}$/', $topcode))
		{
			// cep invalido
			// retornar erro gritante para usuario digitar corretamente
			$error->setErrorMessage( $array_errors["-3"] );
			$result->append($error);

			return $result;
		}
		
		// 30 kgs e o peso maximo enviado pelos correios
    	if ($sweight > ($this->getConfigData('maxweight') / $sweightunit) ||
    		$arr["maior"] > ($this->getConfigData('maxweight') / $sweightunit))
    	{
			return false; // silent exit
		}
		
    	// a soma resultante do comprimento + largura + altura não deve superar a 150 cm.
    	if ($arr["largura"] > 50)
    	{
			return false; // silent exit
		}
		
		
		
		// INICIO OBTER RESULTADOS
		
		$wsdl_url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx?wsdl";
		
		try {
		
			$client = new SoapClient($wsdl_url, array("connection_timeout"=>TIMEOUTCONNECTIONVALUE) /* , array('encoding'=>'UTF-8', 'trace' => 1) */ );
			
			$arrayChamada = array(
					'nCdEmpresa' => trim($this->getConfigData('login')), // @todo fazer vir do admin
					'sDsSenha' => trim($this->getConfigData('password')), // @todo fazer vir do admin
			
					'nCdServico' => implode(",",$shipping_methods) , // @todo fazer vir do admin
					'sCepOrigem' => $frompcode,
					'sCepDestino' => $topcode,
			
					'nVlPeso' => $arr["pesosomadomagento"],
			
					// 1 – Formato caixa/pacote	2 – Formato rolo/prisma
					'nCdFormato' => '1', // @todo fazer vir do admin ou CADA PRODUTO TER SEU FORMATO?
					
					'nVlComprimento' => $arr["comprimento"], // @todo caso servico nao utilize, manter em zero
					'nVlAltura' => $arr["altura"], // @todo caso servico nao utilize, manter em zero
					'nVlLargura' => $arr["largura"], // @todo caso servico nao utilize, manter em zero
					
					'nVlDiametro' => '0', // @todo caso servico nao utilize, manter em zero
					
					// (S – Sim, N – Não)
					'sCdMaoPropria' => $maopropria_fee, // @todo fazer vir do admin? cada produto tem o seu?
	
					'nVlValorDeclarado' => $valordeclarado_fee ? $packagevalue : '0', // @todo caso servico nao utilize, manter em zero. fazer vir pelo admin.
	
					// (S – Sim, N – Não)
					'sCdAvisoRecebimento' => $ar_fee
			);
			
			$array_result = $client->CalcPrecoPrazo($arrayChamada)->CalcPrecoPrazoResult->Servicos->cServico;
		} catch (Exception $e) {

			$error = Mage::getModel('shipping/rate_result_error')
				->setCarrier($this->_code)
				->setCarrierTitle($this->getConfigData('title'));
		
			$error->setErrorMessage($this->getConfigData('specificerrmsg'));
			$result->append($error);
			
			return $result;
		}

		
		if (count($shipping_methods) < 2) $array_result = array($array_result);

		foreach($array_result as $correio_result)
        {
        	if (!is_object($correio_result)) continue;
        	if (!isset($correio_result->Erro)) continue;

			if ($correio_result->Erro != 0)
			{
				$error = Mage::getModel('shipping/rate_result_error')
					->setCarrier($this->_code)
					->setCarrierTitle($this->getConfigData('title'));
			
				$errmessage = array_key_exists($correio_result->Erro,$array_errors) ? $array_errors[$correio_result->Erro] : $correio_result->MsgErro;
				$error->setErrorMessage(array_key_exists($correio_result->Codigo,$array_methods) ? $array_methods[$correio_result->Codigo] . ": " . $errmessage : $errmessage); // @todo o tempo dira
				$result->append($error);
				continue;
			}
			
			$shippingPrice = floatval(str_replace(",",".",$correio_result->Valor)) + $this->getConfigData('handling_fee');
			$shippingPrice += floatval(str_replace(",",".",$correio_result->ValorMaoPropria));
			$shippingPrice += floatval(str_replace(",",".",$correio_result->ValorAvisoRecebimento));
			$shippingPrice += floatval(str_replace(",",".",$correio_result->ValorValorDeclarado));
			
			$data_mais = $correio_result->PrazoEntrega + 3;
			
        	if ($shippingPrice <= 0) continue; // silent exit
        	
			$method = Mage::getModel('shipping/rate_result_method');
			$method->setCarrier($this->_code)
				->setCarrierTitle($this->getConfigData('title'))
				->setMethod($correio_result->Codigo)
				->setCost($shippingPrice)
				->setPrice($shippingPrice)
				->setMethodTitle("{$array_methods[$correio_result->Codigo]} ({$correio_result->PrazoEntrega} a $data_mais dias &uacute;teis): ");
				
            $result->append($method);
		}
		
		$this->_result = $result;
		
		$this->_updateFreeMethodQuote($request);
		
		return $this->_result;
    }
    
	/**
	 * Calcula o peso e cúbico dos itens do carrinho.
	 * Retorna o maior, o menor, e a raiz cúbica ajustada
	 * aos valores mínimos
	 *
	 * @return mixed
	 */
	public function getCartWeight($sweight,$sweightunit)
	{
		$items = Mage::getSingleton('checkout/cart')->getItems();
		$cProduct = Mage::getModel('catalog/product');
		
		$LMIN = 11;
		$CMIN = 16;
		$AMIN = 2;
		
		$cubeEach = 0.0;
		$weightEach = 0.0;
					
		foreach($items as $item)
		{
			$cProduct->load($item->getProduct()->getId());
			
			$cubeEach += (($cProduct->getAltura() * $cProduct->getComprimento() * $cProduct->getLargura()) / 4800) * $item->getQty();
			$weightEach += ($cProduct->getWeight()) * $item->getQty();
		}

		$max = max($cubeEach/$sweightunit, $weightEach/$sweightunit, $sweight);
		$medida = ceil(pow($cubeEach*4800,1/3));
		
		$arr = array(
			"maior" => $max,
			"menor" => min($cubeEach, $weightEach),

			"pesocubico" => $cubeEach,
			"pesosomado" => $weightEach,
			"pesosomadomagento" => $sweight,

			"largura" => $medida < $LMIN ? $LMIN : $medida,
			"altura" => $medida < $AMIN ? $AMIN : $medida,
			"comprimento" => $medida < $CMIN ? $CMIN : $medida,
		);
		
		return $arr;
	}

	/**
	 * Get Tracking Info
	 *
	 * @param mixed $tracking
	 * @return mixed
	 */
	public function getTrackingInfo($tracking) {
		$result = $this->getTracking($tracking);
		if ($result instanceof Mage_Shipping_Model_Tracking_Result){
			if ($trackings = $result->getAllTrackings()) {
				return $trackings[0];
			}
		} elseif (is_string($result) && !empty($result)) {
			return $result;
		}

		return false;
	}

	/**
	 * Get Tracking
	 *
	 * @param array $trackings
	 * @return Mage_Shipping_Model_Tracking_Result
	 */
	public function getTracking($trackings) {
		$this->_result = Mage::getModel('shipping/tracking_result');
		foreach ((array) $trackings as $code) {
			$this->_getTracking($code);
		}

		return $this->_result;
	}

	/**
	 * Protected Get Tracking, opens the request to Correios
	 *
	 * @param string $code
	 * @return boolean
	 */
	protected function _getTracking($code) {
		$error = Mage::getModel('shipping/tracking_result_error');
		$error->setTracking($code);
		$error->setCarrier($this->_code);
		$error->setCarrierTitle($this->getConfigData('title'));
		$error->setErrorMessage($this->getConfigData('specificerrmsg'));

		$url = 'http://websro.correios.com.br/sro_bin/txect01$.QueryList';
		$url .= '?P_LINGUA=001&P_TIPO=001&P_COD_UNI=' . $code;
		try {
			$client = new Zend_Http_Client();
			$client->setUri($url);
			$content = $client->request();
		} catch (Exception $e) {
			$this->_result->append($error);
			return false;
		}

		if (!preg_match('#<table ([^>]+)>(.*?)</table>#is', $content->getBody(), $matches)) {
			$this->_result->append($error);
			return false;
		}
		$table = $matches[2];

		if (!preg_match_all('/<tr>(.*)<\/tr>/i', $table, $columns, PREG_SET_ORDER)) {
			$this->_result->append($error);
			return false;
		}	

		$progress = array();
		for ($i = 0; $i < count($columns); $i++) {
			$column = $columns[$i][1];

			$description = '';
			$found = false;
			if (preg_match('/<td rowspan="?2"?/i', $column) && preg_match('/<td rowspan="?2"?>(.*)<\/td><td>(.*)<\/td><td><font color="[A-Z0-9]{6}">(.*)<\/font><\/td>/i', $column, $matches)) {
				if (preg_match('/<td colspan="?2"?>(.*)<\/td>/i', $columns[$i+1][1], $matchesDescription)) {
					$description = str_replace('  ', '', $matchesDescription[1]);
				}

				$found = true;
			} elseif (preg_match('/<td rowspan="?1"?>(.*)<\/td><td>(.*)<\/td><td><font color="[A-Z0-9]{6}">(.*)<\/font><\/td>/i', $column, $matches)) {
				$found = true;
			}

			if ($found)
			{
				$datetime = explode(' ', $matches[1]);

				//Substituido função Zend_Locale('America/Sao_Paulo') por Zend_Locale();
				//$locale = new Zend_Locale('America/Sao_Paulo');
				$locale = new Zend_Locale();
				//$date = '';
				$date = new Zend_Date($datetime[0], 'dd/MM/YYYY', $locale);

				$track = array(
					'deliverydate' => $date->toString('YYYY-MM-dd', 'America/Sao_Paulo'),
					'deliverytime' => $datetime[1] . ':00',
					'deliverylocation' => $matches[2],
					'status' => $matches[3],
					'activity' => $matches[3]
				);
				
				if ($description !== '') {
					$track['activity'] = $matches[3] . ' - ' . utf8_encode($description);
				}

				$progress[] = $track;
			}
		}

		if (!empty($progress)) {
			$track = $progress[0];
			$track['progressdetail'] = $progress;

			$tracking = Mage::getModel('shipping/tracking_result_status');
			$tracking->setTracking($code);
			$tracking->setCarrier($this->_code);
			$tracking->setCarrierTitle($this->getConfigData('title'));
			$tracking->addData($track);

			$this->_result->append($tracking);
			return true;
		} else {
			$this->_result->append($error);
			return false;
		}
	}

	/**
	 * Returns the allowed carrier methods
	 *
	 * @return array
	 */
	public function getAllowedMethods()
	{
		return array($this->_code => $this->getConfigData('title'));
	}

	/**
	 * Define ZIP Code as required
	 *
	 * @return boolean
	 */
	public function isZipCodeRequired($countryId = null)
	{
		return true;
	}
}
