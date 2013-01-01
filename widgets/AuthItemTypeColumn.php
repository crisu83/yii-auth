<?php
/**
 * AuthItemTypeColumn class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.widgets
 */

/**
 * Grid column for displaying the type for an authorization item row.
 */
class AuthItemTypeColumn extends AuthItemColumn
{
	/**
	 * Initializes the column.
	 */
	public function init()
	{
		if (isset($this->htmlOptions['class']))
			$this->htmlOptions['class'] .= ' item-type-column';
		else
			$this->htmlOptions['class'] = 'item-type-column';
	}

	/**
	 * Renders the data cell content.
	 * @param integer $row the row number (zero-based).
	 * @param mixed $data the data associated with the row.
	 */
	protected function renderDataCellContent($row, $data)
	{
		/* @var $am CAuthManager|AuthBehavior */
		$am = Yii::app()->getAuthManager();

		$labelType = $this->active || $am->hasParent($this->itemName, $data['name']) || $am->hasChild($this->itemName, $data['name'])
			? 'info'
			: '';

		/* @var $controller AuthItemController */
		$controller = $this->grid->getController();

		$controller->widget('bootstrap.widgets.TbLabel', array(
			'type' => $labelType,
			'label' => $controller->getItemTypeText($data['item']->type),
		));
	}
}
