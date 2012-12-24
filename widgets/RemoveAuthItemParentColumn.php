<?php

class RemoveAuthItemParentColumn extends RemoveAuthItemColumn
{
	protected function renderDataCellContent($row, $data)
	{
		if (Yii::app()->authManager->hasItemChild($data->name, $this->itemName))
		{
			$this->grid->owner->widget('bootstrap.widgets.TbButton', array(
				'type'=>'link',
				'size'=>'mini',
				'icon'=>'remove',
				'url'=>array('removeParent', 'itemName'=>$this->itemName, 'parentName'=>$data->name),
				'htmlOptions'=>array('rel'=>'tooltip', 'title'=>Yii::t('AuthModule.main', 'Remove')),
			));
		}
	}
}
