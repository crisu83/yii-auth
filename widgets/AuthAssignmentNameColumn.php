<?php
/**
 * AuthAssignmentNameColumn class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.widgets
 */

/**
 * Grid column for displaying the name of the user for an assignment row.
 */
class AuthAssignmentNameColumn extends AuthAssignmentColumn
{
	/**
	 * Initializes the column.
	 */
	public function init()
	{
		if (isset($this->htmlOptions['class']))
			$this->htmlOptions['class'] .= ' name-column';
		else
			$this->htmlOptions['class'] = 'name-column';
	}

	/**
	 * Renders the data cell content.
	 * @param integer $row the row number (zero-based).
	 * @param mixed $data the data associated with the row.
	 */
	protected function renderDataCellContent($row, $data)
	{
		echo CHtml::link(CHtml::value($data, $this->nameColumn), array('view', 'id'=>$data->{$this->idColumn}));
	}
}
