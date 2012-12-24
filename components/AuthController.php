<?php

class AuthController extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout = 'auth.views.layouts.main';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu = array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs = array();

	/**
	 * @param string $type
	 * @param boolean $plural
	 * @return string
	 * @throws CException
	 */
	public function getItemTypeText($type, $plural = true)
	{
		$n = $plural ? 2 : 1;
		switch ($type)
		{
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
				throw new CException('Auth item type "'.$type.'" is valid.');
		}
		return $name;
	}
}
