<?php

class AuthItemForm extends CFormModel
{
    public $name;
    public $description;
    public $bizrule;
    public $data;
    public $type;

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array(
            'name' => Yii::t('AuthModule.main', 'System name'),
            'description' => Yii::t('AuthModule.main', 'Description'),
            'bizrule' => Yii::t('AuthModule.main', 'Business rule'),
            'data' => Yii::t('AuthModule.main', 'Data'),
            'type' => Yii::t('AuthModule.main', 'Type'),
        );
    }

    /**
     * @return array
     */
    public function rules()
    {
        return array(
            array('description, type', 'required'),
            array('name', 'required', 'on' => 'create'),
            array('name', 'length', 'max' => 64),
        );
    }
}
