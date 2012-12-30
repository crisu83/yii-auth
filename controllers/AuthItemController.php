<?php
/**
 * AuthItemController class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.components
 */

/**
 * Controller for authorization item related actions.
 */
class AuthItemController extends AuthController
{
	/**
	 * Returns the filter configurations.
	 * @return array a list of filter configurations.
	 */
	public function filters()
	{
		return array(
			'validateType + index, create',
		);
	}

	/**
	 * Filter method for validating the item type.
	 * @param CFilterChain $filterChain the filter chain that the filter is on.
	 */
	public function filterValidateType($filterChain)
	{
		$validTypes = array(CAuthItem::TYPE_OPERATION, CAuthItem::TYPE_TASK, CAuthItem::TYPE_ROLE);
		if (isset($_GET['type']) && in_array($_GET['type'], $validTypes))
			$filterChain->run();
	}

	/**
	 * Displays a list of items of the given type.
	 * @param string $type the item type (0=operation, 1=task, 2=role).
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
	 * Displays a form for creating a new item of the given type.
	 * @param string $type the item type (0=operation, 1=task, 2=role).
	 */
	public function actionCreate($type)
	{
		$model = new AuthItemForm('create');

		if (isset($_POST['AuthItemForm']))
		{
			$model->attributes = $_POST['AuthItemForm'];
			if ($model->validate())
			{
				/* @var $am CAuthManager|AuthBehavior */
				$am = Yii::app()->getAuthManager();

				if (($item = $am->getAuthItem($model->name)) === null)
					$item = $am->createAuthItem($model->name, $model->type, $model->description);

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
	 * Displays a form for updating the item with the given name.
	 * @param string $name name of the item.
	 */
	public function actionUpdate($name)
	{
		/* @var $am CAuthManager|AuthBehavior */
		$am = Yii::app()->getAuthManager();

		$item = $am->getAuthItem($name);

		if ($item === null)
			throw new CHttpException(404, Yii::t('AuthModule.main', 'Page not found.'));

		$model = new AuthItemForm('update');

		if (isset($_POST['AuthItemForm']))
		{
			$model->attributes = $_POST['AuthItemForm'];
			if ($model->validate())
			{
				$item->description = $model->description;
				$am->saveAuthItem($item);
				$this->redirect(array('index', 'type' => $model->type));
			}
		}

		$model->name = $name;
		$model->description = $item->description;
		$model->type = $item->type;

		$this->render('update', array(
			'item' => $item,
			'model' => $model,
		));
	}

	/**
	 * Displays the item with the given name.
	 * @param string $name name of the item.
	 */
	public function actionView($name)
	{
		$formModel = new AddAuthItemForm();

		/* @var $am CAuthManager|AuthBehavior */
		$am = Yii::app()->getAuthManager();

		if (isset($_POST['AddAuthItemForm']))
		{
			$formModel->attributes = $_POST['AddAuthItemForm'];
			if ($formModel->validate())
			{
				if (!$am->hasItemChild($name, $formModel->items))
					$am->addItemChild($name, $formModel->items);
			}
		}

		$item = $am->getAuthItem($name);

		$dpConfig = array(
			'pagination' => false,
			'sort' => array('defaultOrder' => 'depth asc'),
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
			'ancestorDp' => $ancestorDp,
			'descendantDp' => $descendantDp,
			'formModel' => $formModel,
			'childOptions' => $childOptions,
		));
	}

	/**
	 * Deletes the item with the given name.
	 * @throws CHttpException if the item does not exist or if the request is invalid.
	 */
	public function actionDelete()
	{
		if (isset($_GET['name']))
		{
			$name = $_GET['name'];

			/* @var $am CAuthManager|AuthBehavior */
			$am = Yii::app()->getAuthManager();

			$item = $am->getAuthItem($name);
			if ($item instanceof CAuthItem)
			{
				$type = $item->type;
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
	 * Removes the parent from the item with the given name.
	 * @param string $itemName name of the item.
	 * @param string $parentName name of the parent.
	 */
	public function actionRemoveParent($itemName, $parentName)
	{
		/* @var $am CAuthManager|AuthBehavior */
		$am = Yii::app()->getAuthManager();

		if ($am->hasItemChild($parentName, $itemName))
			$am->removeItemChild($parentName, $itemName);

		$this->redirect(array('view', 'name' => $itemName));
	}

	/**
	 * Removes the child from the item with the given name.
	 * @param string $itemName name of the item.
	 * @param string $childName name of the child.
	 */
	public function actionRemoveChild($itemName, $childName)
	{
		/* @var $am CAuthManager|AuthBehavior */
		$am = Yii::app()->getAuthManager();

		if ($am->hasItemChild($itemName, $childName))
			$am->removeItemChild($itemName, $childName);

		$this->redirect(array('view', 'name' => $itemName));
	}

	/**
	 * Returns a list of possible children for the item with the given name.
	 * @param string $itemName name of the item.
	 * @return array the child options.
	 */
	protected function getItemChildOptions($itemName)
	{
		$options = array();

		/* @var $am CAuthManager|AuthBehavior */
		$am = Yii::app()->getAuthManager();
		$item = $am->getAuthItem($itemName);
		if ($item instanceof CAuthItem)
		{
			$exclude = $am->getAncestors($itemName);
			$exclude[$itemName] = $item;
			$exclude = array_merge($exclude, $item->getChildren());
			$authItems = $am->getAuthItems();
			$validChildTypes = $this->getValidChildTypes($item->type);

			foreach ($authItems as $childName => $childItem)
			{
				if (in_array($childItem->type, $validChildTypes) && !isset($exclude[$childName]))
					$options[ucfirst($this->getItemTypeText($childItem->type, true))][$childName] = $childItem->description;
			}
		}

		return $options;
	}

	/**
	 * Returns a list of the valid child types for the given type.
	 * @param string $type the item type (0=operation, 1=task, 2=role).
	 * @return array the valid types.
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