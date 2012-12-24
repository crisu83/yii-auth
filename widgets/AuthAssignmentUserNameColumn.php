<?php

class AuthAssignmentUserNameColumn extends AuthAssignmentColumn
{
	public $nameColumn;

	public function init()
	{
		if (isset($this->htmlOptions['class']))
			$this->htmlOptions['class'] .= ' name-column';
		else
			$this->htmlOptions['class'] = 'name-column';
	}

	protected function renderDataCellContent($row, $data)
	{
		if (isset($this->nameColumn))
			echo $data->{$this->nameColumn};
	}
}
