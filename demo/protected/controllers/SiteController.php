<?php

class SiteController extends Controller
{
	public $layout = 'column1';

	/**
	 * Displays the front page.
	 */
	public function actionIndex()
	{
		$this->redirect(array('/auth/assignment/index'));
	}

	public function actionChangeLanguage($locale)
	{
		if (in_array($locale, array_keys(Yii::app()->languages)))
			Yii::app()->user->setState('__locale', $locale);

		$this->redirect(array('index', 'language'=>$locale));
	}

	/**
	 * Resets the database for the demo application.
	 */
	public function actionReset()
	{
		/* @var $db CDbConnection */
		$db = Yii::app()->getComponent('db');
		$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'schema.sql';
		if (file_exists($filename))
		{
			$schema = file_get_contents($filename);
			$schema = preg_split("/;\s+/", trim($schema, ';'));
			foreach ($schema as $sql)
				$db->createCommand($sql)->execute();
		}
		Yii::app()->user->setFlash('success', 'Demo reset.');
		$this->redirect(array('index'));
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if ($error = Yii::app()->errorHandler->error)
		{
			if (Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}
}