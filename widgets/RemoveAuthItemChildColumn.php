<?php

class RemoveAuthItemChildColumn extends RemoveAuthItemColumn
{
	protected function renderDataCellContent($row, $data)
	{
		if (Yii::app()->authManager->hasItemChild($this->itemName, $data->name))
		{
			$this->grid->owner->widget('bootstrap.widgets.TbButton', array(
				'type'=>'link',
				'size'=>'mini',
				'icon'=>'remove',
				'url'=>array('removeChild', 'itemName'=>$this->itemName, 'childName'=>$data->name),
				'htmlOptions'=>array('rel'=>'tooltip', 'title'=>Yii::t('AuthModule.main', 'Remove')),
			));
		}
	}
}
