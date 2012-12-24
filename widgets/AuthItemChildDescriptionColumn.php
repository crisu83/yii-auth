<?php

class AuthItemChildDescriptionColumn extends AuthItemDescriptionColumn
{
	protected function renderDataCellContent($row, $data)
	{
		if (Yii::app()->authManager->hasItemChild($this->itemName, $data->name))
			echo CHtml::link($data->description, array('/auth/authItem/view', 'name'=>$data->name));
		else
			echo '<span class="disabled">'.$data->description.'</span>';
	}
}
