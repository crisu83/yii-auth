<?php

class AuthFilter extends CFilter
{
    /**
     * @param CFilterChain $filterChain
     * @return boolean
     * @throws CHttpException
     */
    protected function preFilter($filterChain)
    {
        $itemName = '';
        $controller = $filterChain->controller;

        if (($module = $controller->getModule()) !== null)
            $itemName .= $module->getId() . '.';

        $itemName .= $controller->getId();

        /* @var $user CWebUser */
        $user = Yii::app()->getUser();

        if ($user->checkAccess($itemName . '.*'))
            return true;

        $itemName .= '.' . $controller->action->getId();
        if ($user->checkAccess($itemName))
            return true;

        throw new CHttpException(401, Yii::t('AuthModule.main', 'Access denied.'));
    }
}
