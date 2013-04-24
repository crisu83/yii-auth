<?php
/* @var $this OperationController|TaskController|RoleController */
/* @var $model AuthItemForm */
/* @var $form TbActiveForm */

$this->breadcrumbs = array(
	$this->capitalize($this->getTypeText(true)) => array('index'),
	Yii::t('AuthModule.main', 'New {type}', array('{type}' => $this->getTypeText())),
);
?>

<h1><?php echo Yii::t('AuthModule.main', 'New {type}', array('{type}' => $this->getTypeText())); ?></h1>

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm'); ?>

<?php echo $form->hiddenField($model, 'type'); ?>
<?php echo $form->textFieldRow($model, 'name'); ?>
<?php echo $form->textFieldRow($model, 'description'); ?>

<div class="form-actions">
	<?php echo TbHtml::submitButton(Yii::t('AuthModule.main', 'Create'),array(
		'style'=>TbHtml::STYLE_PRIMARY,
	)); ?>
	<?php echo TbHtml::linkButton(Yii::t('AuthModule.main', 'Cancel'),array(
		'style'=>TbHtml::STYLE_LINK,
		'url' => array('index'),
	)); ?>
</div>

<?php $this->endWidget(); ?>
