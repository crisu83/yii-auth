<?php

class AuthItemParentDescriptionColumn extends AuthItemDescriptionColumn
{
	protected function renderDataCellContent($row, $data)
	{
		if (Yii::app()->authManager->hasItemChild($data->name, $this->itemName))
			echo CHtml::link($data->description, array('/auth/authItem/view', 'name'=>$data->name));
		else
			echo $data->description;
	}
}
