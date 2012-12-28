<?php
/* @var $this AssignmentController */
/* @var $userDp CActiveDataProvider */

$this->breadcrumbs = array(
    Yii::t('AuthModule.main', 'Assignments'),
);
?>

<h1><?php echo Yii::t('AuthModule.main', 'Assignments'); ?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type' => 'striped hover',
    'dataProvider' => $userDp,
    'columns' => array(
        array(
            'header' => Yii::t('AuthModule.main', 'User'),
            'class' => 'AuthAssignmentNameColumn',
            'nameColumn' => $this->module->userNameColumn,
        ),
        array(
            'header' => Yii::t('AuthModule.main', 'Items'),
            'class' => 'AuthAssignmentItemsColumn',
        ),
        array(
            'class' => 'AuthAssignmentViewColumn',
            'idColumn' => $this->module->userIdColumn,
        ),
    ),
)); ?>
