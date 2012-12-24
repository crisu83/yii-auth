<?php

Yii::import('zii.widgets.grid.CGridColumn');

class AuthItemTypeColumn extends AuthItemRelativeColumn
{
	public function init()
	{
		if (isset($this->htmlOptions['class']))
			$this->htmlOptions['class'] .= ' type-column';
		else
			$this->htmlOptions['class'] = 'type-column';
	}

	public function getLabelCssClass($itemName, $childName)
	{
		return Yii::app()->authManager->hasItemChild($itemName, $childName) ? 'info' : '';
	}

	protected function renderDataCellContent($row, $data)
	{
		/* @var $controller AuthItemController */
		$controller = $this->grid->getOwner();
		$controller->widget('bootstrap.widgets.TbLabel', array(
			'type'=>'info',
			'label'=>$controller->getItemTypeText($data->type, false),
		));
	}
}
