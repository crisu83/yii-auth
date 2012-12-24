<?php

class AuthAssignmentViewUserColumn extends AuthAssignmentColumn
{
	public $idColumn;

	public function init()
	{
		if (isset($this->htmlOptions['class']))
			$this->htmlOptions['class'] .= ' actions-column';
		else
			$this->htmlOptions['class'] = 'actions-column';
	}

	protected function renderDataCellContent($row, $data)
	{
		if (isset($this->idColumn))
		{
			$this->grid->owner->widget('bootstrap.widgets.TbButton', array(
				'type'=>'link',
				'size'=>'mini',
				'icon'=>'eye-open',
				'url'=>array('view', 'id'=>$data->{$this->idColumn}),
				'htmlOptions'=>array('rel'=>'tooltip', 'title'=>Yii::t('AuthModule.main', 'View')),
			));
		}
	}
}
