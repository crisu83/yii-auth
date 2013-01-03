<?php
/**
 * AuthItemColumn class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.widgets
 */

/**
 * Grid column for displaying the description for an authorization item row.
 */
class AuthItemDescriptionColumn extends AuthItemColumn
{
	/**
	 * Initializes the column.
	 */
	public function init()
	{
		if (isset($this->htmlOptions['class']))
			$this->htmlOptions['class'] .= ' item-description-column';
		else
			$this->htmlOptions['class'] = 'item-description-column';
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

		$linkCssClass = $this->active || $am->hasParent($this->itemName, $data['name']) || $am->hasChild($this->itemName, $data['name'])
			? 'active'
			: 'disabled';

		/* @var $controller AuthItemController */
		$controller = $this->grid->getController();

		echo CHtml::link($data['item']->description,
				array('/auth/' . $controller->getItemControllerId($data['item']->type) . '/view', 'name' => $data['name']),
				array( 'class' => $linkCssClass));
	}
}
