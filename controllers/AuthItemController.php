<?php

class AuthItemController extends AuthController
{
    /**
     * @return array
     */
    public function filters()
    {
        return array(
            'validateType + index, create',
        );
    }

    /**
     * @param CFilterChain $filterChain
     */
    public function filterValidateType($filterChain)
    {
        $validTypes = array(CAuthItem::TYPE_OPERATION, CAuthItem::TYPE_TASK, CAuthItem::TYPE_ROLE);
        if (isset($_GET['type']) && in_array($_GET['type'], $validTypes))
            $filterChain->run();
    }

    /**
     * @param string $type
     */
    public function actionIndex($type)
    {
        $dataProvider = new AuthItemDataProvider();
        $dataProvider->type = $type;

        $this->render('index', array(
            'dataProvider' => $dataProvider,
            'type' => $type,
        ));
    }

    /**
     * @param string $type
     */
    public function actionCreate($type)
    {
        $model = new AuthItemForm('create');

        if (isset($_POST['AuthItemForm']))
        {
            $model->attributes = $_POST['AuthItemForm'];
            if ($model->validate())
            {
                $item = Yii::app()->authManager->createAuthItem($model->name, $model->type, $model->description);
                $this->redirect(array('view', 'name' => $item->name));
            }
        }

        $model->type = $type;

        $this->render('create', array(
            'type' => $type,
            'model' => $model,
        ));
    }

    /**
     * @param string $name
     */
    public function actionUpdate($name)
    {
        /* @var $item CAuthItem */
        $item = Yii::app()->authManager->getAuthItem($name);

        if ($item === null)
            throw new CHttpException(404, 'Page not found.');

        $model = new AuthItemForm('update');
        $model->name = $name;

        if (isset($_POST['AuthItemForm']))
        {
            $model->attributes = $_POST['AuthItemForm'];
            if ($model->validate())
            {
                $item->description = $model->description;
                Yii::app()->authManager->saveAuthItem($item);
                $this->redirect(array('index', 'type' => $model->type));
            }
        }

        $model->description = $item->getDescription();
        $model->type = $item->getType();

        $this->render('update', array(
            'item' => $item,
            'model' => $model,
        ));
    }

    /**
     * @param string $name
     */
    public function actionView($name)
    {
        $formModel = new AuthItemsForm();

        /* @var $am CAuthManager|AuthBehavior */
        $am = Yii::app()->getAuthManager();

        if (isset($_POST['AuthItemsForm']))
        {
            $formModel->attributes = $_POST['AuthItemsForm'];
            if ($formModel->validate())
                $am->addItemChild($name, $formModel->items);
        }

        $item = $am->loadAuthItem($name);

        $dpConfig = array(
			'pagination' => false,
			'sort' => array(
				'defaultOrder' => 'depth asc',
			),
		);

        $ancestors = $am->getAncestors($name);
        $ancestorDp = new PermissionDataProvider(array_values($ancestors), $dpConfig);

        $descendants = $am->getDescendants($name);
        $descendantDp = new PermissionDataProvider(array_values($descendants), $dpConfig);

        $childOptions = $this->getItemChildOptions($item->name);
        if (!empty($childOptions))
            $childOptions = array_merge(array('' => Yii::t('AuthModule.main', 'Select item') . ' ...'), $childOptions);

        $this->render('view', array(
            'item' => $item,
            'ancestors' => $ancestors,
            'ancestorDp' => $ancestorDp,
            'descendants' => $descendants,
            'descendantDp' => $descendantDp,
            'formModel' => $formModel,
            'childOptions' => $childOptions,
        ));
    }

    /**
     * @throws CHttpException
     */
    public function actionDelete()
    {
        if (isset($_GET['name']))
        {
            $name = $_GET['name'];

            /* @var $am CAuthManager|AuthBehavior */
            $am = Yii::app()->getAuthManager();

            $item = $am->loadAuthItem($name);
            if ($item instanceof CAuthItem)
			{
				$type = $item->getType();
                $am->removeAuthItem($name);

				if (!isset($_POST['ajax']))
					$this->redirect(array('index', 'type' => $type));
			}
			else
				throw new CHttpException(404, Yii::t('AuthModule.main', 'Item does not exist.'));
        }
        else
            throw new CHttpException(400, Yii::t('AuthModule.main', 'Invalid request.'));
    }

    /**
     * @param string $itemName
     * @param string $parentName
     */
    public function actionRemoveParent($itemName, $parentName)
    {
        Yii::app()->authManager->removeItemChild($parentName, $itemName);
        $this->redirect(array('view', 'name' => $itemName));
    }

    /**
     * @param string $itemName
     * @param string $childName
     */
    public function actionRemoveChild($itemName, $childName)
    {
        Yii::app()->authManager->removeItemChild($itemName, $childName);
        $this->redirect(array('view', 'name' => $itemName));
    }

    /**
     * @param string $itemName
     * @return array
     */
    protected function getItemChildOptions($itemName)
    {
        $options = array();
        /* @var $am CAuthManager|AuthBehavior */
        $am = Yii::app()->getAuthManager();
        $item = $am->loadAuthItem($itemName);
        $type = $item->getType();
        $exclude = $am->getAncestors($itemName);
        $exclude = array_merge($exclude, $item->getChildren());
        $authItems = $am->loadAuthItems();
        foreach ($authItems as $name => $item)
        {
            $validChildTypes = $this->getValidChildTypes($type);
            if (in_array($item->type, $validChildTypes) && !isset($exclude[$name]) && $name !== $itemName)
                $options[ucfirst($this->getItemTypeText($item->getType()))][$name] = $item->getDescription();
        }

        return $options;
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getValidChildTypes($type)
    {
        $validTypes = array();
        switch ($type)
        {
            case CAuthItem::TYPE_OPERATION:
                break;

            case CAuthItem::TYPE_TASK:
                $validTypes[] = CAuthItem::TYPE_OPERATION;
                break;

            case CAuthItem::TYPE_ROLE:
                $validTypes[] = CAuthItem::TYPE_OPERATION;
                $validTypes[] = CAuthItem::TYPE_TASK;
                break;
        }
        if (!$this->module->strictMode)
            $validTypes[] = $type;

        return $validTypes;
    }
}