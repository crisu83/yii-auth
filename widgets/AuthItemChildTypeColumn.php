<?php

class AuthItemChildTypeColumn extends AuthItemTypeColumn
{
	protected function renderDataCellContent($row, $data)
	{
		/* @var $controller AuthItemController */
		$controller = $this->grid->getOwner();
		$controller->widget('bootstrap.widgets.TbLabel', array(
			'type'=>$this->getLabelCssClass($this->itemName, $data->name),
			'label'=>$controller->getItemTypeText($data->type, false),
		));
	}
}
