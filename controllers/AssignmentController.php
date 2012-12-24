<?php

class AssignmentController extends AuthController
{
	public function actionIndex()
	{
		$userDp = new CActiveDataProvider($this->module->userClass);

		$this->render('index', array(
			'userDp'=>$userDp,
		));
	}

	/**
	 * @param string $id
	 */
	public function actionView($id)
	{
		$formModel = new AuthItemsForm();

		/* @var $am CAuthManager|AuthBehavior */
		$am = Yii::app()->authManager;

		if (isset($_POST['AuthItemsForm']))
		{
			$formModel->attributes = $_POST['AuthItemsForm'];
			if ($formModel->validate())
				$am->assign($formModel->items, $id);
		}

		$model = CActiveRecord::model($this->module->userClass)->findByPk($id);

		/* @var $am CAuthManager|AuthBehavior */
		$am = Yii::app()->getAuthManager();
		$assignments = $am->loadAuthAssignments($id);
		$authItems = $am->getAuthItemsByNames(array_keys($assignments));
		$authItemDp = new AuthItemDataProvider();
		$authItemDp->setAuthItems($authItems);

		$assignmentOptions = $this->getAssignmentOptions($id);
		if (!empty($assignmentOptions))
			$assignmentOptions = array_merge(array(''=>Yii::t('AuthModule.main', 'Select item').' ...'), $assignmentOptions);

		$this->render('view', array(
			'model'=>$model,
			'authItemDp'=>$authItemDp,
			'formModel'=>$formModel,
			'assignmentOptions'=>$assignmentOptions,
		));
	}

	/**
	 * @throws CHttpException
	 */
	public function actionRevoke()
	{
		if (isset($_GET['itemName'], $_GET['userId']))
		{
			$userId = $_GET['userId'];
			Yii::app()->authManager->revoke($_GET['itemName'], $userId);

			if (!isset($_POST['ajax']))
				$this->redirect(array('view', 'id'=>$userId));
		}
		else
			throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * @param string $userId
	 * @return array
	 */
	protected function getAssignmentOptions($userId)
	{
		$options = array();

		/* @var $am CAuthManager|AuthBehavior */
		$am = Yii::app()->authManager;

		$assignments = $am->loadAuthAssignments($userId);
		$assignedItems = array_keys($assignments);
		$authItems = $am->loadAuthItems();
		foreach ($authItems as $itemName => $item)
		{
			if (!in_array($itemName, $assignedItems))
				$options[ucfirst($this->getItemTypeText($item->getType()))][$itemName] = $item->getDescription();
		}
		return $options;
	}
}