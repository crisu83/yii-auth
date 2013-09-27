<?php
/**
 * AuthController class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.components
 */

/**
 * Base controller for the module.
 * Note: Do NOT extend your controllers from this class!
 */
abstract class AuthController extends CController
{
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();
    /**
     * @var array the breadcrumbs of the current page.
     */
    public $breadcrumbs = array();

    /**
     * Initializes the controller.
     */
    public function init()
    {
        parent::init();
        $this->layout = $this->module->defaultLayout;
        $this->menu = $this->getSubMenu();
    }

    /**
     * Returns the authorization item type as a string.
     * @param string $type the item type (0=operation, 1=task, 2=role).
     * @param boolean $plural whether to return the name in plural.
     * @return string the text.
     * @throws CException if the item type is invalid.
     */
    public function getItemTypeText($type, $plural = false)
    {
        // todo: change the default value for $plural to false.
        $n = $plural ? 2 : 1;
        switch ($type) {
            case CAuthItem::TYPE_OPERATION:
                $name = Yii::t('AuthModule.main', 'operation|operations', $n);
                break;

            case CAuthItem::TYPE_TASK:
                $name = Yii::t('AuthModule.main', 'task|tasks', $n);
                break;

            case CAuthItem::TYPE_ROLE:
                $name = Yii::t('AuthModule.main', 'role|roles', $n);
                break;

            default:
                throw new CException('Auth item type "' . $type . '" is valid.');
        }
        return $name;
    }

    /**
     * Returns the controllerId for the given authorization item.
     * @param string $type the item type (0=operation, 1=task, 2=role).
     * @return string the controllerId.
     * @throws CException if the item type is invalid.
     */
    public function getItemControllerId($type)
    {
        $controllerId = null;
        switch ($type) {
            case CAuthItem::TYPE_OPERATION:
                $controllerId = 'operation';
                break;

            case CAuthItem::TYPE_TASK:
                $controllerId = 'task';
                break;

            case CAuthItem::TYPE_ROLE:
                $controllerId = 'role';
                break;

            default:
                throw new CException('Auth item type "' . $type . '" is valid.');
        }
        return $controllerId;
    }

    /**
     * Capitalizes the first word in the given string.
     * @param string $string the string to capitalize.
     * @return string the capitalized string.
     * @see http://stackoverflow.com/questions/2517947/ucfirst-function-for-multibyte-character-encodings
     */
    public function capitalize($string)
    {
        if (!extension_loaded('mbstring')) {
            return ucfirst($string);
        }

        $encoding = Yii::app()->charset;
        $firstChar = mb_strtoupper(mb_substr($string, 0, 1, $encoding), $encoding);
        return $firstChar . mb_substr($string, 1, mb_strlen($string, $encoding) - 1, $encoding);
    }

    /**
     * Returns the sub menu configuration.
     * @return array the configuration.
     */
    protected function getSubMenu()
    {
        return array(
            array(
                'label' => Yii::t('AuthModule.main', 'Assignments'),
                'url' => array('/auth/assignment/index'),
                'active' => $this instanceof AssignmentController,
            ),
            array(
                'label' => $this->capitalize($this->getItemTypeText(CAuthItem::TYPE_ROLE, true)),
                'url' => array('/auth/role/index'),
                'active' => $this instanceof RoleController,
            ),
            array(
                'label' => $this->capitalize($this->getItemTypeText(CAuthItem::TYPE_TASK, true)),
                'url' => array('/auth/task/index'),
                'active' => $this instanceof TaskController,
            ),
            array(
                'label' => $this->capitalize($this->getItemTypeText(CAuthItem::TYPE_OPERATION, true)),
                'url' => array('/auth/operation/index'),
                'active' => $this instanceof OperationController,
            ),
        );
    }
}
