<?php
/**
 * AuthItemColumn class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.widgets
 */

Yii::import('zii.widgets.grid.CGridColumn');

/**
 * Grid column for displaying authorization item related data.
 */
class AuthItemColumn extends CGridColumn
{
    /**
     * @var string name of the item.
     */
    public $itemName;
    /**
     * @var boolean whether the row should appear activated.
     */
    public $active = false;
}
