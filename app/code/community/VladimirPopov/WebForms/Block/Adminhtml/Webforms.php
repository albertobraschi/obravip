<?php //error_reporting(E_ERROR);?>
<?php 
//$_F=__FILE__;
//$_X='Pz48P3BocA0KY2wxc3MgVmwxZDRtNHJQMnAydl9XNWJGMnJtc19CbDJja19BZG00bmh0bWxfVzViZjJybXMgNXh0NW5kcyBNMWc1X0FkbTRuaHRtbF9CbDJja19XNGRnNXRfR3I0ZF9DMm50MTRuNXJ7DQoJcDNibDRjIGYzbmN0NDJuIF9fYzJuc3RyM2N0KCl7DQoJCSR0aDRzLT5fYzJudHIybGw1ciA9ICcxZG00bmh0bWxfdzViZjJybXMnOw0KCQkkdGg0cy0+X2JsMmNrR3IyM3AgPSAndzViZjJybXMnOw0KCQkkdGg0cy0+X2g1MWQ1clQ1eHQgPSBNMWc1OjpoNWxwNXIoJ3c1YmYycm1zJyktPl9fKCdNMW4xZzUgRjJybXMnKTsNCgkJJHRoNHMtPl8xZGRCM3R0Mm5MMWI1bCA9IE0xZzU6Omg1bHA1cigndzViZjJybXMnKS0+X18oJ0FkZCBONXcgRjJybScpOw0KCQlwMXI1bnQ6Ol9fYzJuc3RyM2N0KCk7DQoNCgkJJGYycm1zID0gTTFnNTo6ZzV0TTJkNWwoJ3c1YmYycm1zL3c1YmYycm1zJyktPmc1dEMybGw1Y3Q0Mm4oKS0+YzIzbnQoKTsNCgkJDQoJCTRmKCRmMnJtcz49byl7DQoJCQkkdGg0cy0+X3I1bTJ2NUIzdHQybignMWRkJyk7DQoJCQkkdGg0cy0+XzFkZEIzdHQybignMWRkJywxcnIxeSgNCgkJCQknbDFiNWwnID0+IE0xZzU6Omg1bHA1cigndzViZjJybXMnKS0+X18oJ0FkZCBONXcgRjJybScpLA0KCQkJCScybmNsNGNrJyA9PiAnMWw1cnQoXCcnLk0xZzU6Omg1bHA1cigndzViZjJybXMnKS0+X18oJ1kyMyBoMXY1IHI1MWNoNWQgQzJtbTNuNHR5IEVkNHQ0Mm4gbDRtNHQhXG5DMm1tM240dHkgRWQ0dDQybiAxbGwyd3MgeTIzIHQyIG0xbjFnNSAybmx5IG8gdzViLWYycm1zLlxuVXBncjFkNSB0MiBQcjJmNXNzNDJuMWwgRWQ0dDQybi4nKS4nXCcpJywNCgkJCSkpOw0KCQl9DQoJfQ0KfSAgDQo/Pg0K';
//eval(base64_decode('JF9YPWJhc2U2NF9kZWNvZGUoJF9YKTskX1g9c3RydHIoJF9YLCcxMjM0NTZhb3VpZScsJ2FvdWllMTIzNDU2Jyk7JF9SPWVyZWdfcmVwbGFjZSgnX19GSUxFX18nLCInIi4kX0YuIiciLCRfWCk7ZXZhbCgkX1IpOyRfUj0wOyRfWD0wOw=='));
//$_X=base64_decode($_X);$_X=strtr($_X,'123456aouie','aouie123456'); //$_R=ereg_replace('__FILE__',"'".$_F."'",$_X);eval($_R);$_R=0;$_X=0;
?>

<?php
class VladimirPopov_WebForms_Block_Adminhtml_Webforms extends Mage_Adminhtml_Block_Widget_Grid_Container{
	public function __construct(){
		$this->_controller = 'adminhtml_webforms';
		$this->_blockGroup = 'webforms';
		$this->_headerText = Mage::helper('webforms')->__('Manage Forms');
		$this->_addButtonLabel = Mage::helper('webforms')->__('Add New Form');
		parent::__construct();

		$forms = Mage::getModel('webforms/webforms')->getCollection()->count();
		
		/*
		if($forms>=3){
			$this->_removeButton('add');
			$this->_addButton('add',array(
					'label' => Mage::helper('webforms')->__('Add New Form'),
					'onclick' => 'alert(\''.Mage::helper('webforms')->__('You have reached Community Edition limit!\nCommunity Edition allows you to manage only 3 web-forms.\nUpgrade to Professional Edition.').'\')',
			));
		}
		*/
	}
}