<?php
/**
 * OperationController class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.controllers
 */

/**
 * Controller for operation related actions.
 */
class OperationController extends AuthItemController
{
    /**
     * @var integer the item type (0=operation, 1=task, 2=role).
     */
    public $type = CAuthItem::TYPE_OPERATION;
}
