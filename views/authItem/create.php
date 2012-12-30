<?php
/* @var $this AuthItemController */
/* @var $model AuthItemForm */
/* @var $type string */
/* @var $form TbActiveForm */
?>

<h1><?php echo Yii::t('AuthModule.main', 'New {type}', array('{type}' => $this->getItemTypeText($type))); ?></h1>

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'type'=>'horizontal',
)); ?>

<?php echo $form->hiddenField($model, 'type'); ?>
<?php echo $form->textFieldRow($model, 'name'); ?>
<?php echo $form->textFieldRow($model, 'description'); ?>

<div class="form-actions">
	<?php $this->widget('bootstrap.widgets.TbButton', array(
		'buttonType' => 'submit',
		'type' => 'primary',
		'label' => Yii::t('AuthModule.main', 'Create'),
	)); ?>
	<?php $this->widget('TbButton', array(
		'type' => 'link',
		'label' => Yii::t('AuthModule.main', 'Cancel'),
		'url' => array('index', 'type' => $type),
	)); ?>
</div>

<?php $this->endWidget(); ?>