<?php

Yii::import('zii.widgets.grid.CGridColumn');

class AuthItemColumn extends CGridColumn
{
    /**
     * @var string
     */
    public $itemName;
    /**
     * @var boolean
     */
    public $active = false;
}
