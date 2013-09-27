<?php
/**
 * AddAuthItemForm class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.models
 */

/**
 * Form model for displaying a list of authorization items.
 */
class AddAuthItemForm extends CFormModel
{
    /**
     * @var array a list of authorization items.
     */
    public $items;

    /**
     * Returns the attribute labels.
     * @return array attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'items' => Yii::t('AuthModule.main', 'Items'),
        );
    }

    /**
     * Returns the validation rules for attributes.
     * @return array validation rules.
     */
    public function rules()
    {
        return array(
            array('items', 'required'),
        );
    }
}
