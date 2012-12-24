<?php

class AuthItemsForm extends CFormModel
{
	public $items;

	/**
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'items' => Yii::t('AuthModule.main', 'Items'),
		);
	}

	/**
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('items', 'required'),
		);
	}
}
