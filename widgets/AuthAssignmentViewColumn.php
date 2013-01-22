<?php
/**
 * AuthAssignmentViewColumn class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.widgets
 */

/**
 * Grid column for displaying the view link for an assignment row.
 */
class AuthAssignmentViewColumn extends AuthAssignmentColumn
{
	/**
	 * Initializes the column.
	 */
	public function init()
	{
		if (isset($this->htmlOptions['class']))
			$this->htmlOptions['class'] .= ' actions-column';
		else
			$this->htmlOptions['class'] = 'actions-column';
	}

	/**
	 * Renders the data cell content.
	 * @param integer $row the row number (zero-based).
	 * @param mixed $data the data associated with the row.
	 */
	protected function renderDataCellContent($row, $data)
	{
		if (!Yii::app()->user->isAdmin)
		{
			$this->grid->controller->widget('bootstrap.widgets.TbButton', array(
				'type' => 'link',
				'size' => 'mini',
				'icon' => 'eye-open',
				'url' => array('view', 'id' => $data->{$this->idColumn}),
				'htmlOptions' => array('rel' => 'tooltip', 'title' => Yii::t('AuthModule.main', 'View')),
			));
		}
	}
}
