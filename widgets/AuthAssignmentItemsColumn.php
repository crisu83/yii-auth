<?php

class AuthAssignmentItemsColumn extends AuthAssignmentColumn
{
	public $type;

	protected function renderDataCellContent($row, $data)
	{
		/* @var $am CAuthManager|AuthBehavior */
		$am = Yii::app()->getAuthManager();
		$assignments = $am->loadAuthAssignments($data->id);
		$items = $am->getAuthItemsByNames(array_keys($assignments));
		foreach ($items as $itemName => $item)
		{
			if ($item->getType() === $this->type)
				echo $item->description.'<br />';
		}
	}
}
