<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to suporte.developer@buscape-inc.com so we can send you a copy immediately.
 *
 * @category   Buscape
 * @package    Buscape_Ebit
 * @copyright  Copyright (c) 2010 Buscapé Company (http://www.buscapecompany.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Buscape_Ebit_Block_Template_Seal extends Mage_Core_Block_Template
{

    protected function _getConfig()
    {
        return Mage::getModel('ebit/config');
    }
    
    protected function _toHtml()
    {
        if (!Mage::helper('ebit')->isSealAvailable()) {
            return '';
        }
        
        return '
        <!-- BEGIN EBIT SEAL CODE -->    
        <a id="seloEbit" style="float: right;" href="http://www.ebit.com.br/#id_url_ebit" target="_blank" onclick="redir(this.href);">Avaliação de Lojas e-bit</a>
        <script type="text/javascript" id="getSelo" src="https://a248.e.akamai.net/f/248/52872/0s/img.ebit.com.br/ebitBR/selo-ebit/js/getSelo.js?'.$this->_getConfig()->getAccount().'"></script>
        <!-- END EBIT SEAL CODE -->';
        
    }
}