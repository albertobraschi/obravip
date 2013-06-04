/**
 * Indexa - Customer. Adicionar campos ao checkout.
 *
 * @title      Magento -> Indexa Customer module
 * @category   Custom customer fields
 * @package    Indexa_Customer
 * @author     Indexa Development Team -> desenvolvimento [at] indexainternet [dot] com  [dot] br
 * @copyright  Copyright (c) 2011 Indexa - http://www.indexainternet.com.br
 */

Object.extend(Validation, {
    reset : function(elm) {
        elm = $(elm);
        var cn = $w(elm.className);
        cn.each(function(value) {
            var prop = '__advice'+value.camelize();
            if(elm[prop]) {
                var advice = Validation.getAdvice(value, elm);
                if (advice) {
                    advice.parentNode.removeChild(advice);
                }
                elm[prop] = '';
            }
            elm.removeClassName('validation-failed');
            elm.removeClassName('validation-passed');
            if (Validation.defaultOptions.addClassNameToContainer && Validation.defaultOptions.containerClassName != '') {
                var container = elm.up(Validation.defaultOptions.containerClassName);
                if (container) {
                    container.removeClassName('validation-passed');
                    container.removeClassName('validation-error');
                }
            }
        });
    }
});


//algoritmo obtido na internet
Validation.add('validate-cnpj', 'CNPJ inválido.',
	function (cnpj) {
	  var i = 0;
	  var l = 0;
	  var strNum = "";
	  var strMul = "6543298765432";
	  var character = "";
	  var iValido = 1;
	  var iSoma = 0;
	  var strNum_base = "";
	  var iLenNum_base = 0;
	  var iLenMul = 0;
	  var iSoma = 0;
	  var strNum_base = 0;
	  var iLenNum_base = 0;
	  
	  cnpj = cnpj.match(/\d/g).join('');

	  if (cnpj == "")
	        return false;

	  l = cnpj.length;
	  for (i = 0; i < l; i++) {
	        caracter = cnpj.substring(i,i+1)
	        if ((caracter >= '0') && (caracter <= '9'))
	           strNum = strNum + caracter;
	  };

	  if(strNum.length != 14)
	        return false;

	  strNum_base = strNum.substring(0,12);
	  iLenNum_base = strNum_base.length - 1;
	  iLenMul = strMul.length - 1;
	  for(i = 0;i < 12; i++)
	        iSoma = iSoma +
	                        parseInt(strNum_base.substring((iLenNum_base-i),(iLenNum_base-i)+1),10) *
	                        parseInt(strMul.substring((iLenMul-i),(iLenMul-i)+1),10);

	  iSoma = 11 - (iSoma - Math.floor(iSoma/11) * 11);
	  if(iSoma == 11 || iSoma == 10)
	        iSoma = 0;

	  strNum_base = strNum_base + iSoma;
	  iSoma = 0;
	  iLenNum_base = strNum_base.length - 1
	  for(i = 0; i < 13; i++)
	        iSoma = iSoma +
	                        parseInt(strNum_base.substring((iLenNum_base-i),(iLenNum_base-i)+1),10) *
	                        parseInt(strMul.substring((iLenMul-i),(iLenMul-i)+1),10);

	  iSoma = 11 - (iSoma - Math.floor(iSoma/11) * 11);
	  if(iSoma == 11 || iSoma == 10)
	        iSoma = 0;
	  
	  strNum_base = strNum_base + iSoma;
	  if(strNum != strNum_base)
	        return false;

	  return true;
	}
);

//algoritmo obtido na internet
Validation.add('validate-cpf', 'CPF inválido.',
	function valida_cpf(cpf) {
		var numeros, digitos, soma, i, resultado, digitos_iguais;
		digitos_iguais = 1;
		
		cpf = cpf.match(/\d/g).join('');
		
		if (cpf.length < 11)
			return false;
		for (i = 0; i < cpf.length - 1; i++)
			if (cpf.charAt(i) != cpf.charAt(i + 1)) {
				digitos_iguais = 0;
				break;
			}
		if (!digitos_iguais) {
			numeros = cpf.substring(0, 9);
			digitos = cpf.substring(9);
			soma = 0;
			for (i = 10; i > 1; i--)
				soma += numeros.charAt(10 - i) * i;
			resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
			if (resultado != digitos.charAt(0))
				return false;
			numeros = cpf.substring(0, 10);
			soma = 0;
			for (i = 11; i > 1; i--)
				soma += numeros.charAt(11 - i) * i;
			resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
			if (resultado != digitos.charAt(1))
				return false;
			return true;
		} else
			return false;
	}
);


Validation.add('not-validate', '',
		function(v){
			return true;
		}
);