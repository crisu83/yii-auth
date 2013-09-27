<?php
/**
 * AuthFilter class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.components
 */

/**
 * Filter that automatically checks if the user has access to the current controller action.
 */
class AuthFilter extends CFilter
{
    /**
     * @var array name-value pairs that would be passed to business rules associated
     * with the tasks and roles assigned to the user.
     */
    public $params = array();

    /**
     * Performs the pre-action filtering.
     * @param CFilterChain $filterChain the filter chain that the filter is on.
     * @return boolean whether the filtering process should continue and the action should be executed.
     * @throws CHttpException if the user is denied access.
     */
    protected function preFilter($filterChain)
    {
        $itemName = '';
        $controller = $filterChain->controller;

        /* @var $user CWebUser */
        $user = Yii::app()->getUser();

        if (($module = $controller->getModule()) !== null) {
            $itemName .= $module->getId() . '.';
            if ($user->checkAccess($itemName . '*')) {
                return true;
            }
        }

        $itemName .= $controller->getId();
        if ($user->checkAccess($itemName . '.*')) {
            return true;
        }

        $itemName .= '.' . $controller->action->getId();
        if ($user->checkAccess($itemName, $this->params)) {
            return true;
        }

        if ($user->isGuest) {
            $user->loginRequired();
        }

        throw new CHttpException(401, Yii::t('yii', 'You are not authorized to perform this action.'));
    }
}
