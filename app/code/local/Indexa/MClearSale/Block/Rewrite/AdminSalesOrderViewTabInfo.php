<?php
/**
 * Indexa - MClearSale Anti-Fraud Module
 *
 * @title      Magento -> Indexa MClearSale module
 * @category   Payment Anti-Fraud
 * @package    Indexa_MClearSale
 * @author     Indexa Development Team -> desenvolvimento [at] indexainternet [dot] com  [dot] br
 * @copyright  Copyright (c) 2009 Indexa - http://www.indexainternet.com.br
 */

class Indexa_MClearSale_Block_Rewrite_AdminSalesOrderViewTabInfo extends Mage_Adminhtml_Block_Sales_Order_View_Tab_Info
{
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        
        $start = strpos($html,'<!--Payment Method-->');
        $start = strpos($html,"</fieldset>",$start);
        
        $html_1 = substr($html,0,$start);
        $html_2 = substr($html,$start);
		
        $urlModel = Mage::getModel("adminhtml/url");
        
        $html_meio  = "<iframe src='' name='mclearsale' id='mclearsale' height='100' frameborder='0' scrolling='no'></iframe>";

        $html_meio .= "
        <script type=\"text/javascript\">
        var globalQuantasVezes = 0;
        function loadMclearsale()
        {
    		$('mclearsale').src = '".Mage::getUrl(      'mclearsale/mclearsale/updatefield/order_id/'.Mage::registry('current_order')->getId().'/key/'.$urlModel->getSecretKey('mclearsale','updatefield').'/')."';
    	    $('mclearsale').location  = '".Mage::getUrl('mclearsale/mclearsale/updatefield/order_id/'.Mage::registry('current_order')->getId().'/key/'.$urlModel->getSecretKey('mclearsale','updatefield').'/')."';
	        $('mclearsale').contentDocument.location  = '".Mage::getUrl('mclearsale/mclearsale/updatefield/order_id/'.Mage::registry('current_order')->getId().'/key/'.$urlModel->getSecretKey('mclearsale','updatefield').'/')."';
    	}
    	function loadMclearsaleTab(tab)
    	{
    		try {
	    		if ($(tab.tab).id == 'sales_order_view_tabs_order_info' && globalQuantasVezes == 0)
	    		{
	    			loadMclearsale();
	    			globalQuantasVezes += 1;
	    		}
    		} catch (e) {}
    	}
    	</script>
        <script type=\"text/javascript\"> Event.observe(window, 'load', loadMclearsale); </script>
        <script type=\"text/javascript\"> varienGlobalEvents.attachEventHandler('showTab', loadMclearsaleTab); </script>
        ";
        

        return $html_1.$html_meio.$html_2;
    }
#----------------------------------------------------------------------------------------------    
    protected function _afterToHtml2($html)
    {
        $html = parent::_afterToHtml($html);
        
        $start = strpos($html,'<!--Payment Method-->');
        $start = strpos($html,"</fieldset>",$start);
        
        $html_1 = substr($html,0,$start);
        $html_2 = substr($html,$start);
		
        $urlModel = Mage::getModel("adminhtml/url");
        $html_meio = "<iframe src='$url' name='mclearsale' id='mclearsale' height='3000' width='40000' frameborder='0' scrolling='no'></iframe>";

        $html_meio .= "
        <script type=\"text/javascript\">
        var globalQuantasVezes = 0;
        function loadMclearsale()
        {
    	    $('mclearsale').src = '".Mage::getUrl(      'mclearsale/mclearsale/updatefield/order_id/'.Mage::registry('current_order')->getId().'/key/'.$urlModel->getSecretKey('mclearsale','updatefield').'/')."';
    	    $('mclearsale').location  = '".Mage::getUrl('mclearsale/mclearsale/updatefield/order_id/'.Mage::registry('current_order')->getId().'/key/'.$urlModel->getSecretKey('mclearsale','updatefield').'/')."';
	        $('mclearsale').contentDocument.location  = '".Mage::getUrl('mclearsale/mclearsale/updatefield/order_id/'.Mage::registry('current_order')->getId().'/key/'.$urlModel->getSecretKey('mclearsale','updatefield').'/')."';
    	}
    	function loadMclearsaleTab(tab)
    	{
    		try {
	    		if ($(tab.tab).id == 'sales_order_view_tabs_order_info' && globalQuantasVezes == 0)
	    		{
	    			loadMclearsale();
	    			globalQuantasVezes += 1;
	    		}
    		} catch (e) {}
    	}
    	</script>
        <script type=\"text/javascript\"> Event.observe(window, 'load', loadMclearsale); </script>
        <script type=\"text/javascript\"> varienGlobalEvents.attachEventHandler('showTab', loadMclearsaleTab); </script>
        ";

        return $html_1.$html_meio.$html_2;
    }
}
