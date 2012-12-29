<?php
/**
 * AuthAssignmentColumn class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.widgets
 */

Yii::import('zii.widgets.grid.CGridColumn');

/**
 * Grid column for displaying assignment related data.
 */
class AuthAssignmentColumn extends CGridColumn
{
	/**
	 * @var integer the user id.
	 */
	public $userId;
}
