<?php 
/*
 * 
	Login: shoeshop.
	Senha: shoe3shop.
	Seu código de integração é: 9761dade-b719-487c-9355-52843f4e9d74.
 * 
 * */

/**
 * Indexa - MClearSale Anti-Fraud Module
 *
 * @title      Magento -> Indexa MClearSale module
 * @category   Payment Anti-Fraud
 * @package    Indexa_MClearSale
 * @author     Indexa Development Team -> desenvolvimento [at] indexainternet [dot] com  [dot] br
 * @copyright  Copyright (c) 2009 Indexa - http://www.indexainternet.com.br
 */

class Indexa_MClearSale_MclearsaleController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() {}

    public function updatefieldAction()
    {
    	//ini_set('allow_url_fopen', 1);
    	
		$dados_envio 	= array();
		$request 		= Mage::app()->getRequest();
		$order 			= Mage::getModel('sales/order')->load($request->getParam('order_id'));
		$order_data   	= $order->getData();
		$billing 		= $order->getBillingAddress();
		$billing_data 	= $billing->getData();
		$shipping 		= $order->getShippingAddress();
		$shipping_data	= $shipping->getData();
		
		#---- DADOS DA COMPRA ----#
		$dados['CodigoIntegracao'] = trim( Mage::getConfig()->getNode('default/mclearsale/indexa_general/codigo_painel') );
		$dados['PedidoID'] = $order->getIncrementId();
		$dados['Data'] = date('d/m/Y H:i:s', strtotime($order_data['created_at']));
		$dados['Total'] = number_format($order_data['grand_total'], 2, ',', ',');
		
		$tipo_pgto = $order->getPayment()->getData('method');
		$pagamentoTipo = '';
		$pagamentoBandeiraCartao = '';
		
    if (		
    		stripos($tipo_pgto,"visa") 		!== false ||
			stripos($tipo_pgto,"master") 	!== false ||
			stripos($tipo_pgto,"redecard") 	!== false ||
			stripos($tipo_pgto,"diners") 	!== false ||
			stripos($tipo_pgto,"amex") 		!== false ||
			stripos($tipo_pgto,"american") 	!== false ||
			stripos($tipo_pgto,"hipercard") !== false ||
			stripos($tipo_pgto,"aura") 		!== false
		)
		{
			$pagamentoTipo = '1';
			
			if (stripos($tipo_pgto,"visa") !== false)
			{
				$pagamentoBandeiraCartao = '3';
			}
			else if (stripos($tipo_pgto,"master") !== false)
			{
				$pagamentoBandeiraCartao = '2';
			}
			else if (stripos($tipo_pgto,"redecard") !== false)
			{
				$pagamentoBandeiraCartao = '4';
			}
			else if (stripos($tipo_pgto,"diners") !== false)
			{
				$pagamentoBandeiraCartao = '1';
			}
			else if(stripos($tipo_pgto,"amex") !== false ||
					stripos($tipo_pgto,"american") !== false)
			{
				$pagamentoBandeiraCartao = '5';
			}
			else if (stripos($tipo_pgto,"hipercard") !== false)
			{
				$pagamentoBandeiraCartao = '6';
			}
			else if (stripos($tipo_pgto,"aura") !== false)
			{
				$pagamentoBandeiraCartao = '7';
			}
		}
		else if(stripos($tipo_pgto,"boleto") !== false ||
				stripos($tipo_pgto,"bloqueto") !== false
			)
		{
			$pagamentoTipo = '2';
		}
    	else if(stripos($tipo_pgto,"checkmo") !== false ||
				stripos($tipo_pgto,"checkmo") !== false
			)
		{
			$pagamentoTipo = '3';
		}
		
		$dados['TipoPagamento'] = $pagamentoTipo; 
		if($pagamentoBandeiraCartao)	$dados['TipoCartao'] = $pagamentoBandeiraCartao;
		$dados['Parcelas'] = '1'; // @todo Nao sabe-se quantos são.
		
		#---- CLIENTE COBRANCA ----#
		$dados['Cobranca_Nome']						= $billing->getName();
		$dados['Cobranca_Email']					= $order_data['customer_email'];
		$dados['Cobranca_Documento']				= preg_replace('/[^0-9]/', '', $order_data['customer_taxvat']);
		$dados['Cobranca_Logradouro']				= $billing_data['street'];
		$dados['Cobranca_Logradouro_Numero']		= '0';
		$dados['Cobranca_Logradouro_Complemento']	= '';
		$dados['Cobranca_Bairro']					= '-';
		$dados['Cobranca_Cidade']					= $billing_data['city'];
		$dados['Cobranca_Estado']					= $billing->getRegionCode();
		$dados['Cobranca_CEP']						= preg_replace('/[^0-9]/', '', $billing_data['postcode'] );
		$dados['Cobranca_Pais']						= ($billing_data['country_id'] == 'BR') ? 'BRA' : $billing_data['country_id'];
		
		$telephone 									= preg_replace('/[^0-9]/', '', $billing->getTelephone());
		$dados['Cobranca_DDD_Telefone']				= (strlen($telephone) > 8) ? substr($telephone, 0, 2) : '00';
		$dados['Cobranca_Telefone']					= (strlen($telephone) > 8) ? substr($telephone, 2) : '00000000';
		
		#---- ENTREGA ----#
		$dados['Entrega_Nome'] 						= $shipping->getName();
		$dados['Entrega_Email'] 					= $order_data['customer_email'];
		$dados['Entrega_Documento'] 				= preg_replace('/[^0-9]/', '', $order_data['customer_taxvat']);
		$dados['Entrega_Logradouro'] 				= $shipping_data['street'];
		$dados['Entrega_Logradouro_Numero'] 		= '0';
		$dados['Entrega_Logradouro_Complemento'] 	= '';
		$dados['Entrega_Bairro'] 					= '-';
		$dados['Entrega_Cidade'] 					= $shipping_data['city'];
		$dados['Entrega_Estado'] 					= $shipping->getRegionCode();
		$dados['Entrega_CEP'] 						= preg_replace('/[^0-9]/', '', $shipping_data['postcode']);
		$dados['Entrega_Pais'] 						= ($shipping_data['country_id'] == 'BR') ? 'BRA' : $shipping_data['country_id'];
		
		$telephone 									= preg_replace('/[^0-9]/', '', $shipping->getTelephone());
		$dados['Entrega_DDD_Telefone'] 				= (strlen($telephone) > 8) ? substr($telephone, 0, 2) : '00';
		$dados['Entrega_Telefone'] 					= (strlen($telephone) > 8) ? substr($telephone, 2) : '00000000';
		
		
		#---- PRODUTOS ----#
		$items 		= $order->getAllItems();
		$product 	= Mage::getModel('catalog/product');
		$mclearsale = Mage::getModel('mclearsale/MClearSale');
		
		$i = 1;
		foreach ($items as $id => $item)
		{
			if ($item->getParentItem()) continue;
			
			$_itemData = $item->getData();
			$product->load($_itemData["product_id"]);
			
			$dados["Item_ID_$i"] 		= $item->getSku();
			$dados["Item_Nome_$i"]		= $item->getName();
			$dados["Item_Qtd_$i"]  		= $item->getQtyOrdered()*1;
			$dados["Item_Valor_$i"]  	= number_format($item->getPrice(), 2, ',', '');
			$dados["Item_Categoria_$i"] = $mclearsale->getCategoryName($product);
			
			$i++;
		}
		
		$request = array();
		foreach($dados as $key => $dado)
		{ 
			$request[] = $key .'='. urlencode($dado);	
		}

		$url = 'https://www.clearsale.com.br/integracaov2/freeclearsale/frame.aspx?'. implode('&', $request);

		echo "<iframe src='$url' name='mclearsale' id='mclearsale' frameborder='0' scrolling='no'></iframe>";
    }
#----------------------------------------------------------------------------------------------------------    
 	public function updatefieldAction_OLD()
    {

		ini_set("allow_url_fopen", 1);
		
		$identificacao = (string) Mage::getConfig()->getNode('default/mclearsale/indexa_general/codigo_painel');
		$modulo = "MCLEARSALE";
		$operacao = "AnaliseRisco";
		$ambiente = "PRODUCAO";
		
        $request = Mage::app()->getRequest();
        Zend_Debug::dump($request); die;
        $order = Mage::getModel('sales/order')->load($request->getParam('order_id'));
        
        $_totalData = $order->getData();
        $_arraydatacreated = explode(" ",$_totalData['created_at']);
        
		$pedidoNumero = $order->getIncrementId();
		$pedidoData = implode("-",array_reverse(explode("-", $_arraydatacreated[0])))." ".$_arraydatacreated[1];
		$pedidoIp =  $order->getRemoteIp();
		$pedidoValorTotal = number_format($_totalData['grand_total'], 2, '', '');
		$pedidoParcelas = "1"; // @todo Indexa : nao temos como descobrir no momento

		
		
        /* PARA USAR O INDEXA_CUSTOMER */
        $cliente = Mage::getModel('customer/customer');
        $cliente->load($order->getCustomerId());
        if ($cliente->getCpf()) $_totalData['customer_taxvat'] = $cliente->getCpf();
        /* FIM PARA USAR INDEXA_CUSTOMER */
		
		

//		$metodosMclear = array(	1 => "Cartão de Crédito (Visa, Redecard, Mastercard, Diners, American Express, Hipercard, Aura)",
//								2 => "Bloqueto Bancário",
//								3 => "Débito Bancário",
//								4 => "Débito Bancário – Dinheiro",
//								5 => "Débito Bancário – Cheque",
//								6 => "Transferência Bancária",
//								7 => "Sedex a Cobrar",
//								8 => "Cheque",
//								9 => "Dinheiro",
//								10 => "Financiamento",
//								11 => "Fatura",
//								12 => "Cupom",
//								13 => "Multicheque",
//								14 => "Outros" );


		$_paymentData = $order->getPayment()->getData();
		$pagamentoTipo = "";
		$pagamentoBandeiraCartao = "";
		
		//Verificação adicionada para compatabilizar o módulo da ClearSale com os 2 módulos de pagamento da Locaweb
		//locaweb_method para versão nova
		//method para versão antiga
		if(isset($_paymentData['locaweb_method']) && !($_paymentData['locaweb_method'] == "")) {
			//echo "locaweb_method";die;
			$variavelMetodo = "locaweb_method";
		} else {
			//echo "method";die;
			$variavelMetodo = "method";
		}
		
		if (	stripos($_paymentData["$variavelMetodo"],"visa") !== false ||
				stripos($_paymentData["$variavelMetodo"],"master") !== false ||
				stripos($_paymentData["$variavelMetodo"],"redecard") !== false ||
				stripos($_paymentData["$variavelMetodo"],"diners") !== false ||
				stripos($_paymentData["$variavelMetodo"],"amex") !== false ||
				stripos($_paymentData["$variavelMetodo"],"american") !== false ||
				stripos($_paymentData["$variavelMetodo"],"hipercard") !== false ||
				stripos($_paymentData["$variavelMetodo"],"aura") !== false
			)
		{
			$pagamentoTipo = 1;
			
			if (stripos($_paymentData["$variavelMetodo"],"visa") !== false)
			{
				$pagamentoBandeiraCartao = 3;
			}
			else if (stripos($_paymentData["$variavelMetodo"],"master") !== false)
			{
				$pagamentoBandeiraCartao = 2;
			}
			else if (stripos($_paymentData["$variavelMetodo"],"redecard") !== false)
			{
				$pagamentoBandeiraCartao = 4;
			}
			else if (stripos($_paymentData["$variavelMetodo"],"diners") !== false)
			{
				$pagamentoBandeiraCartao = 1;
			}
			else if(stripos($_paymentData["$variavelMetodo"],"amex") !== false ||
					stripos($_paymentData["$variavelMetodo"],"american") !== false)
			{
				$pagamentoBandeiraCartao = 5;
			}
			else if (stripos($_paymentData["$variavelMetodo"],"hipercard") !== false)
			{
				$pagamentoBandeiraCartao = 6;
			}
			else if (stripos($_paymentData["$variavelMetodo"],"aura") !== false)
			{
				$pagamentoBandeiraCartao = 7;
			}
		}
		else if(stripos($_paymentData["$variavelMetodo"],"boleto") !== false ||
				stripos($_paymentData["$variavelMetodo"],"bloqueto") !== false
			)
		{
			$pagamentoTipo = 2;
		}
    	else if(stripos($_paymentData["$variavelMetodo"],"checkmo") !== false ||
				stripos($_paymentData["$variavelMetodo"],"checkmo") !== false
			)
		{
			$pagamentoTipo = 3;
		}
		
		// Itens do pedido
		
		$items = $order->getAllItems();
		$itemcount = count($items);
		
		$pedidoItensItemCodigo = array();
		$pedidoItensItemDescricao = array();
		$pedidoItensItemCategoria = array();
		$pedidoItensItemQuantidade = array();
		$pedidoItensItemValorUnitario = array();
		
		$product = Mage::getModel('catalog/product');
		$mclearsale = Mage::getModel('mclearsale/MClearSale');
		
		$i = 0;
		foreach ($items as $itemId => $item)
		{
			if ($item->getParentItem()) continue;
			
			$_itemData = $item->getData();
			$product->load($_itemData["product_id"]);
			
			$pedidoItensItemCodigo[$i] = $item->getSku();
			$pedidoItensItemDescricao[$i] = $item->getName();
			$pedidoItensItemCategoria[$i] = $mclearsale->getCategoryName($product);
			$pedidoItensItemQuantidade[$i] = $item->getQtyOrdered()*1;
			$pedidoItensItemValorUnitario[$i] = number_format($item->getPrice(), 2, '', '');
			
			$i++;
		}
		
		$_ba = $order->getBillingAddress();
		
		$_baData = $_ba->getData();
		
        $cust_ddd = '00';
        $cust_telephone = preg_replace("/[^0-9]/", "", $_ba->getTelephone());
        $cust_telephone = str_replace(" ","",$cust_telephone);
        if (($st = strlen($cust_telephone)-8) > 0)
        {
            $cust_ddd = substr($cust_telephone, 0, 2);
	    	$cust_telephone = substr($cust_telephone, $st, 8);
        }
        if(!ereg("^[0-9]{8}$", $cust_telephone))
		{
			$cust_telephone = "00000000";
		}
		
        if (!(isset($_totalData['customer_taxvat']) && strlen($_totalData['customer_taxvat']) > 0))
        {
        	$_totalData['customer_taxvat'] = "0";
        }
        
        $region_code = $_ba->getRegionCode();
        if (strlen($region_code) > 2)
        {
        	$region_code = substr($region_code,0,2);
        }
        else if (strlen($region_code) < 2)
        {
        	$region_code = "00";
        }
        
        $topcode = preg_replace("/[^0-9]/","", $_baData['postcode'] );
        if(!ereg("^[0-9]{8}$", $topcode))
		{
			$topcode = "00000000";
		}
        
		$cobrancaNome = $_ba->getName();
		$cobrancaEmail = $_totalData['customer_email'];
		$cobrancaDocumento = preg_replace("/[^0-9]/","",$_totalData['customer_taxvat']); 
		$cobrancaEndereco = $_baData['street'];
		$cobrancaNumero = "0";
		$cobrancaComplemento = "";
		$cobrancaBairro = "-";
		$cobrancaCidade = $_baData['city'];
		$cobrancaCep = $topcode;
		$cobrancaEstado = $region_code;
		$cobrancaPais = $_baData['country_id'] == "BR" ? "BRA" : $_baData['country_id'];
		$cobrancaDddTelefone = $cust_ddd;
		$cobrancaTelefone = $cust_telephone;
		$cobrancaDddCelular = "";
		$cobrancaCelular = "";
			
    	
		$_ba = $order->getShippingAddress();
		$_baData = $_ba->getData();
		
        $cust_ddd = '00';
        $cust_telephone = preg_replace("/[^0-9]/", "", $_ba->getTelephone());
        $cust_telephone = str_replace(" ","",$cust_telephone);
        if (($st = strlen($cust_telephone)-8) > 0)
        {
            $cust_ddd = substr($cust_telephone, 0, 2);
	    	$cust_telephone = substr($cust_telephone, $st, 8);
        }
        if(!ereg("^[0-9]{8}$", $cust_telephone))
		{
			$cust_telephone = "00000000";
		}
        
        $region_code = $_ba->getRegionCode();
        if (strlen($region_code) > 2)
        {
        	$region_code = substr($region_code,0,2);
        }
        else if (strlen($region_code) < 2)
        {
        	$region_code = "00";
        }
        
        $topcode = preg_replace("/[^0-9]/","", $_baData['postcode'] );
        if(!ereg("^[0-9]{8}$", $topcode))
		{
			$topcode = "00000000";
		}
		
		$entregaNome = $_ba->getName();
		$entregaEmail = $_totalData['customer_email'];
		$entregaDocumento = preg_replace("/[^0-9]/","",$_totalData['customer_taxvat']);
		$entregaEndereco = $_baData['street'];
		$entregaNumero = "0";
		$entregaComplemento = "";
		$entregaBairro = "-";
		$entregaCidade = $_baData['city'];
		$entregaCep = $topcode;
		$entregaEstado = $region_code;
		$entregaPais = $_baData['country_id'] == "BR" ? "BRA" : $_baData['country_id'];
		$entregaDddTelefone = $cust_ddd;
		$entregaTelefone = $cust_telephone;
		$entregaDddCelular = "";
		$entregaCelular = "";
		
		$xmlRequisicao = '<?xml version="1.0" encoding="utf-8" ?>';
		$xmlRequisicao .= '<Locaweb>';
		    $xmlRequisicao .= '<Pedido>';
		        $xmlRequisicao .= '<Numero>'. $pedidoNumero .'</Numero>';
		        $xmlRequisicao .= '<Data>'. $pedidoData .'</Data>';
		        if ($pedidoIp != '') $xmlRequisicao .= '<Ip>'. $pedidoIp .'</Ip>';
		        $xmlRequisicao .= '<ValorTotal>'. $pedidoValorTotal .'</ValorTotal>';
		        $xmlRequisicao .= '<Parcelas>'. $pedidoParcelas .'</Parcelas>';
		        $xmlRequisicao .= '<Pagamento>';
		            $xmlRequisicao .= '<Tipo>'. $pagamentoTipo .'</Tipo>';
		            if ($pagamentoBandeiraCartao != '') $xmlRequisicao .= '<BandeiraCartao>'. $pagamentoBandeiraCartao .'</BandeiraCartao>';
		        $xmlRequisicao .= '</Pagamento>';
		        $xmlRequisicao .= '<Itens>';
		        for ($i = 0; $i < count($pedidoItensItemCodigo); $i++)
		        {
		            $xmlRequisicao .= '<Item>';
		                $xmlRequisicao .= '<Codigo>'. $pedidoItensItemCodigo[$i] .'</Codigo>';
		                $xmlRequisicao .= '<Descricao>'. $pedidoItensItemDescricao[$i] .'</Descricao>';
		                if ($pedidoItensItemCategoria[$i] != '' && $pedidoItensItemCategoria[$i] != false) $xmlRequisicao .= '<Categoria>'. $pedidoItensItemCategoria[$i] .'</Categoria>';
		                $xmlRequisicao .= '<Quantidade>'. $pedidoItensItemQuantidade[$i] .'</Quantidade>';
		                $xmlRequisicao .= '<ValorUnitario>'. $pedidoItensItemValorUnitario[$i] .'</ValorUnitario>';
		            $xmlRequisicao .= '</Item>';
		        }
		        $xmlRequisicao .= '</Itens>';
		        $xmlRequisicao .= '<Cobranca>';
		            $xmlRequisicao .= '<Nome>'. $cobrancaNome .'</Nome>';
		            $xmlRequisicao .= '<Email>'. $cobrancaEmail .'</Email>';
		            $xmlRequisicao .= '<Documento>'. $cobrancaDocumento .'</Documento>';
		            $xmlRequisicao .= '<Endereco>'. $cobrancaEndereco .'</Endereco>';
		            $xmlRequisicao .= '<Numero>'. $cobrancaNumero .'</Numero>';
		            if ($cobrancaComplemento != '') $xmlRequisicao .= '<Complemento>'. $cobrancaComplemento .'</Complemento>';
		            $xmlRequisicao .= '<Bairro>'. $cobrancaBairro .'</Bairro>';
		            $xmlRequisicao .= '<Cidade>'. $cobrancaCidade .'</Cidade>';
		            if ($cobrancaCep != '') $xmlRequisicao .= '<Cep>'. $cobrancaCep .'</Cep>';
		            $xmlRequisicao .= '<Estado>'. $cobrancaEstado .'</Estado>';
		            $xmlRequisicao .= '<Pais>'. $cobrancaPais .'</Pais>';
		            $xmlRequisicao .= '<DddTelefone>'. $cobrancaDddTelefone .'</DddTelefone>';
		            if ($cobrancaTelefone != '') $xmlRequisicao .= '<Telefone>'. $cobrancaTelefone .'</Telefone>';
		            if ($cobrancaDddCelular != '') $xmlRequisicao .= '<DddCelular>'. $cobrancaDddCelular .'</DddCelular>';
		            if ($cobrancaCelular != '') $xmlRequisicao .= '<Celular>'. $cobrancaCelular .'</Celular>';
		        $xmlRequisicao .= '</Cobranca>';
		        $xmlRequisicao .= '<Entrega>';
		            $xmlRequisicao .= '<Nome>'. $entregaNome .'</Nome>';
		            $xmlRequisicao .= '<Email>'. $entregaEmail .'</Email>';
		            $xmlRequisicao .= '<Documento>'. $entregaDocumento .'</Documento>';
		            $xmlRequisicao .= '<Endereco>'. $entregaEndereco .'</Endereco>';
		            $xmlRequisicao .= '<Numero>'. $entregaNumero .'</Numero>';
		            if ($entregaComplemento != '') $xmlRequisicao .= '<Complemento>'. $entregaComplemento .'</Complemento>';
		            $xmlRequisicao .= '<Bairro>'. $entregaBairro .'</Bairro>';
		            $xmlRequisicao .= '<Cidade>'. $entregaCidade .'</Cidade>';
		            if ($entregaCep != '') $xmlRequisicao .= '<Cep>'. $entregaCep .'</Cep>';
		            $xmlRequisicao .= '<Estado>'. $entregaEstado .'</Estado>';
		            $xmlRequisicao .= '<Pais>'. $entregaPais .'</Pais>';
		            $xmlRequisicao .= '<DddTelefone>'. $entregaDddTelefone .'</DddTelefone>';
		            if ($entregaTelefone != '') $xmlRequisicao .= '<Telefone>'. $entregaTelefone .'</Telefone>';
		            if ($entregaDddCelular != '') $xmlRequisicao .= '<DddCelular>'. $entregaDddCelular .'</DddCelular>';
		            if ($entregaCelular != '') $xmlRequisicao .= '<Celular>'. $entregaCelular .'</Celular>';
		        $xmlRequisicao .= '</Entrega>';
		    $xmlRequisicao .= '</Pedido>';
		$xmlRequisicao .= '</Locaweb>';
		$xmlRequisicao = str_replace("&","e",$xmlRequisicao);
		
		$urlLocawebCE = "https://comercio.locaweb.com.br/comercio.comp";
		
		$request = "identificacao=" . $identificacao;
		$request .= "&modulo=" . $modulo;
		$request .= "&operacao=" . $operacao;
		$request .= "&ambiente=" . $ambiente;
		$request .= "&xml=" . urlencode($xmlRequisicao);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $urlLocawebCE);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$retorno = curl_exec($ch);
		curl_close($ch);
		    
		echo utf8_encode($retorno);

    }
}