<?php
/* @var $this AssignmentController */
/* @var $userDp CActiveDataProvider */

$this->breadcrumbs=array(
	Yii::t('AuthModule.main', 'Assignments'),
);
?>

<h1><?php echo Yii::t('AuthModule.main', 'Assignments'); ?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'type' => 'striped hover',
	'dataProvider'=>$userDp,
	'columns'=>array(
		array(
			'class'=>'AuthAssignmentUserNameColumn',
			'nameColumn'=>$this->module->userNameColumn,
		),
		array(
			'header'=>ucfirst($this->getItemTypeText(CAuthItem::TYPE_ROLE)),
			'class'=>'AuthAssignmentItemsColumn',
			'type'=>CAuthItem::TYPE_ROLE,
			'headerHtmlOptions'=>array('class'=>'assignment-type-column'),
		),
		array(
			'header'=>ucfirst($this->getItemTypeText(CAuthItem::TYPE_TASK)),
			'class'=>'AuthAssignmentItemsColumn',
			'type'=>CAuthItem::TYPE_TASK,
			'headerHtmlOptions'=>array('class'=>'assignment-type-column'),
		),
		array(
			'header'=>ucfirst($this->getItemTypeText(CAuthItem::TYPE_OPERATION)),
			'class'=>'AuthAssignmentItemsColumn',
			'type'=>CAuthItem::TYPE_OPERATION,
			'headerHtmlOptions'=>array('class'=>'assignment-type-column'),
		),
		array(
			'class'=>'AuthAssignmentViewUserColumn',
			'idColumn'=>$this->module->userIdColumn,
		),
	),
)); ?>
