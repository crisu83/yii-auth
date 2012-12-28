<?php

class AuthModule extends CWebModule
{
    public $defaultController = 'assignment';
    public $appLayout = 'application.views.layouts.main';
    public $strictMode = true;
    public $forceCopyAssets = false;
    public $users = array('admin');

    public $userClass = 'User';
    public $userIdColumn = 'id';
    public $userNameColumn = 'name';

    private $_assetsUrl;

    public function init()
    {
        // import the module-level models and components
        $this->setImport(
            array(
                'auth.components.*',
                'auth.models.*',
                'auth.widgets.*',
            )
        );

        $this->registerCss();
    }

    public function registerCss()
    {
        Yii::app()->clientScript->registerCssFile($this->getAssetsUrl() . '/css/auth.css');
    }

    /**
     * @param CController $controller
     * @param CAction $action
     * @return boolean
     * @throws CHttpException
     */
    public function beforeControllerAction($controller, $action)
    {
        if (parent::beforeControllerAction($controller, $action))
        {
            if (!in_array(Yii::app()->user->getName(), $this->users))
                throw new CHttpException(401, 'Access denied.');

            return true;
        }
        else
            return false;
    }

    /**
     * Returns the URL to the published assets folder.
     * @return string the URL
     */
    protected function getAssetsUrl()
    {
        if (isset($this->_assetsUrl))
            return $this->_assetsUrl;
        else
        {
            $assetsPath = Yii::getPathOfAlias('auth.assets');
            $assetsUrl = Yii::app()->assetManager->publish($assetsPath, false, -1, $this->forceCopyAssets);

            return $this->_assetsUrl = $assetsUrl;
        }
    }
}
