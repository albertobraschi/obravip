<?php 

class Indexa_Matrixrate_Block_Adminhtml_Renderer_Render extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
			$value =  $row->getData($this->getColumn()->getIndex());
			$value = ($value == '') ? '*' : $value;
			
			if(is_numeric($value))
				$value = number_format($value, 3);
			
			return $value;
	}
}