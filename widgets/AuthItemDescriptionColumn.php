<?php

class AuthItemDescriptionColumn extends AuthItemRelativeColumn
{
	public function init()
	{
		if (isset($this->htmlOptions['class']))
			$this->htmlOptions['class'] .= ' description-column';
		else
			$this->htmlOptions['class'] = 'description-column';
	}

	protected function renderDataCellContent($row, $data)
	{
		echo CHtml::link($data->description, array('/auth/authItem/view', 'name'=>$data->name));
	}
}
