<?php
/* @var $this AuthItemController */
/* @var $type string */
/* @var $dataProvider AuthItemDataProvider */

$this->breadcrumbs = array(
	ucfirst($this->getItemTypeText($type)),
);
?>
<h1><?php echo ucfirst($this->getItemTypeText($type)); ?></h1>

<?php $this->widget('bootstrap.widgets.TbButton', array(
	'type' => 'primary',
	'label' => Yii::t('AuthModule.main', 'Add {type}', array('{type}' => $this->getItemTypeText($type, false))),
	'url' => array('create', 'type'=>$type),
)); ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'type' => 'striped hover',
	'dataProvider'=>$dataProvider,
	'emptyText'=>Yii::t('AuthModule.main', 'No {type} found.', array('{type}'=>$this->getItemTypeText($type))),
	'columns'=>array(
		array(
			'name' => 'name',
			'header' => Yii::t('AuthModule.main', 'System name'),
			'headerHtmlOptions'=>array('class'=>'name-column'),
		),
		array(
			'name' => 'description',
			'header' => Yii::t('AuthModule.main', 'Description'),
			'headerHtmlOptions'=>array('class'=>'description-column'),
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'viewButtonUrl' => function($data) {
				return Yii::app()->controller->createUrl('view', array('name'=>$data->name));
			},
			'updateButtonUrl' => function($data) {
				return Yii::app()->controller->createUrl('update', array('name'=>$data->name));
			},
			'deleteButtonUrl' => function($data) {
				return Yii::app()->controller->createUrl('delete', array('name'=>$data->name));
			},
			'deleteConfirmation'=>Yii::t('AuthModule.main', 'Are you sure you want to delete this item?'),
		),
	),
)); ?>
