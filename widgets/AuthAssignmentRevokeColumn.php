<?php

class AuthAssignmentRevokeColumn extends AuthAssignmentColumn
{
	public function init()
	{
		if (isset($this->htmlOptions['class']))
			$this->htmlOptions['class'] .= ' actions-column';
		else
			$this->htmlOptions['class'] = 'actions-column';
	}

	protected function renderDataCellContent($row, $data)
	{
		if ($this->userId !== null)
		{
			$this->grid->owner->widget('bootstrap.widgets.TbButton', array(
				'type'=>'link',
				'size'=>'mini',
				'icon'=>'remove',
				'url'=>array('revoke', 'itemName'=>$data->name, 'userId'=>$this->userId),
				'htmlOptions'=>array('rel'=>'tooltip', 'title'=>Yii::t('AuthModule.main', 'Revoke')),
			));
		}
	}
}
