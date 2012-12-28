<div class="form">

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'comment-form',
	'enableAjaxValidation'=>true,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->textFieldRow($model,'author',array('size'=>60,'maxlength'=>128)); ?>

	<?php echo $form->textFieldRow($model,'email',array('size'=>60,'maxlength'=>128)); ?>

	<?php echo $form->textFieldRow($model,'url',array('size'=>60,'maxlength'=>128)); ?>

	<?php echo $form->textAreaRow($model,'content',array('rows'=>6, 'class'=>'span6')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType' => 'submit',
			'type'=>'primary',
			'label' => $model->isNewRecord ? 'Submit' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->