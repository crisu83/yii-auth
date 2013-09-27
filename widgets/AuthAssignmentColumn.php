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
 *
 * @property string $idColumn name of the user id column.
 * @property string $nameColumn name of the user name column.
 */
class AuthAssignmentColumn extends CGridColumn
{
    /**
     * @var integer the user id.
     */
    public $userId;

    /**
     * Returns the name of the user id column.
     * @return string the column name.
     */
    protected function getIdColumn()
    {
        return $this->grid->controller->module->userIdColumn;
    }

    /**
     * Returns the name of the user name column.
     * @return string the column name.
     */
    protected function getNameColumn()
    {
        return $this->grid->controller->module->userNameColumn;
    }
}
