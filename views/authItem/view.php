<?php
/* @var $this AuthItemController */
/* @var $item CAuthItem */
/* @var $ancestors CAuthItem[] */
/* @var $ancestorDp AuthItemDataProvider */
/* @var $descendants CAuthItem[] */
/* @var $descendantDp AuthItemDataProvider */
/* @var $formModel AuthItemsForm */
/* @var $form TbActiveForm */
/* @var $childOptions array */

$this->breadcrumbs = array(
	ucfirst($this->getItemTypeText($item->type))=>array('index', 'type'=>$item->type),
	$item->description,
);
?>

<div class="title-row clearfix">

	<h1 class="pull-left">
		<?php echo CHtml::encode($item->description); ?>
		<small><?php echo $this->getItemTypeText($item->type, false); ?></small>
	</h1>

	<?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
		'htmlOptions'=>array('class'=>'pull-right'),
		'buttons'=>array(
			array(
				'label'=>Yii::t('AuthModule.main', 'Edit'),
				'url'=>array('update', 'name'=>$item->name),
			),
			array(
				'icon'=>'trash',
				'url'=>array('delete', 'name'=>$item->name),
				'htmlOptions'=>array(
					'confirm'=>Yii::t('AuthModule.main', 'Are you sure you want to delete this item?'),
				),
			),
		),
	)); ?>

</div>

<?php $this->widget('bootstrap.widgets.TbDetailView', array(
    'data' => $item,
    'nullDisplay' => '',
    'attributes' => array(
        array(
            'label' => Yii::t('AuthModule.main', 'System name'),
            'name' => 'name',
        ),
        'description',
        /*
        array(
            'label'=>Yii::t('AuthModule.main', 'Business rule'),
            'name'=>'bizrule',
        ),
        'data',
        */
    ),
)); ?>

<hr />

<div class="row">

	<div class="span6">

		<h3>
			<?php echo Yii::t('AuthModule.main', 'Ancestors'); ?>
			<small><?php echo Yii::t('AuthModule.main', 'Permissions that inherit this item'); ?></small>
		</h3>

		<?php $this->widget('bootstrap.widgets.TbGridView', array(
			'type' => 'striped condensed hover',
			'dataProvider'=>$ancestorDp,
			'emptyText'=>Yii::t('AuthModule.main', 'This is item does not have any ancestors.'),
			'template'=>"{items}",
			'hideHeader'=>true,
			'columns'=>array(
				array(
					'class'=>'AuthItemDescriptionColumn',
					'itemName'=>$item->name,
				),
				array(
					'class'=>'AuthItemTypeColumn',
					'itemName'=>$item->name,
				),
				array(
					'class'=>'AuthItemRemoveColumn',
					'itemName'=>$item->name,
				),
			),
		)); ?>

	</div>

	<div class="span6">

		<h3>
			<?php echo Yii::t('AuthModule.main', 'Descendants'); ?>
			<small><?php echo Yii::t('AuthModule.main', 'Permissions granted by this item'); ?></small>
		</h3>

		<?php $this->widget('bootstrap.widgets.TbGridView', array(
			'type' => 'striped condensed hover',
			'dataProvider'=>$descendantDp,
			'emptyText'=>Yii::t('AuthModule.main', 'This item does not have any descendants.'),
			'hideHeader'=>true,
			'template'=>"{items}",
			'columns'=>array(
				array(
					'class'=>'AuthItemDescriptionColumn',
					'itemName'=>$item->name,
				),
				array(
					'class'=>'AuthItemTypeColumn',
					'itemName'=>$item->name,
				),
				array(
					'class'=>'AuthItemRemoveColumn',
					'itemName'=>$item->name,
				),
			),
		)); ?>

	</div>

</div>

<div class="row">

	<div class="span6 offset6">

		<?php if (!empty($childOptions)): ?>

			<h4><?php echo Yii::t('AuthModule.main', 'Add child'); ?></h4>

			<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
				'type'=>'inline',
			)); ?>

			<?php echo $form->dropDownListRow($formModel, 'items', $childOptions, array('label'=>false)); ?>

			<?php $this->widget('bootstrap.widgets.TbButton', array(
				'buttonType'=>'submit',
				'label'=>Yii::t('AuthModule.main', 'Add'),
			)); ?>

			<?php $this->endWidget(); ?>

		<?php endif; ?>

	</div>

</div>