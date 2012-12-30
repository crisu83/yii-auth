<?php
/**
 * AssignmentController class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.controllers
 */

/**
 * Controller for authorization assignment related actions.
 */
class AssignmentController extends AuthController
{
	/**
	 * Displays the a list of all the assignments.
	 */
	public function actionIndex()
	{
		$dataProvider = new CActiveDataProvider($this->module->userClass);

		$this->render('index', array(
			'dataProvider' => $dataProvider
		));
	}

	/**
	 * Displays the assignments for the user with the given id.
	 * @param string $id the user id.
	 */
	public function actionView($id)
	{
		$formModel = new AddAuthItemForm();

		/* @var $am CAuthManager|AuthBehavior */
		$am = Yii::app()->authManager;

		if (isset($_POST['AddAuthItemForm']))
		{
			$formModel->attributes = $_POST['AddAuthItemForm'];
			if ($formModel->validate())
				$am->assign($formModel->items, $id);
		}

		$model = CActiveRecord::model($this->module->userClass)->findByPk($id);

		/* @var $am CAuthManager|AuthBehavior */
		$am = Yii::app()->getAuthManager();

		$assignments = $am->loadAuthAssignments($id, false);
		$authItems = $am->getItemsPermissions(array_keys($assignments));
		$authItemDp = new AuthItemDataProvider();
		$authItemDp->setAuthItems($authItems);

		$assignmentOptions = $this->getAssignmentOptions($id);
		if (!empty($assignmentOptions))
			$assignmentOptions = array_merge(array('' => Yii::t('AuthModule.main', 'Select item') . ' ...'), $assignmentOptions);

		$this->render('view', array(
			'model' => $model,
			'authItemDp' => $authItemDp,
			'formModel' => $formModel,
			'assignmentOptions' => $assignmentOptions,
		));
	}

	/**
	 * Revokes an assignment from the given user.
	 * @throws CHttpException if the request is invalid.
	 */
	public function actionRevoke()
	{
		if (isset($_GET['itemName'], $_GET['userId']))
		{
			$userId = $_GET['userId'];
			Yii::app()->authManager->revoke($_GET['itemName'], $userId);

			if (!isset($_POST['ajax']))
				$this->redirect(array('view', 'id' => $userId));
		}
		else
			throw new CHttpException(400, Yii::t('AuthModule.main', 'Invalid request.'));
	}

	/**
	 * Returns a list of possible assignments for the user with the given id.
	 * @param string $userId the user id.
	 * @return array the assignment options.
	 */
	protected function getAssignmentOptions($userId)
	{
		$options = array();

		/* @var $am CAuthManager|AuthBehavior */
		$am = Yii::app()->authManager;

		$assignments = $am->loadAuthAssignments($userId, false);
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