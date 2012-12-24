<?php

Yii::import('zii.widgets.grid.CGridColumn');

class AuthItemParentTypeColumn extends AuthItemTypeColumn
{
	protected function renderDataCellContent($row, $data)
	{
		/* @var $controller AuthItemController */
		$controller = $this->grid->getOwner();
		$controller->widget('bootstrap.widgets.TbLabel', array(
			'type'=>$this->getLabelCssClass($data->name, $this->itemName),
			'label'=>$controller->getItemTypeText($data->type, false),
		));
	}
}
