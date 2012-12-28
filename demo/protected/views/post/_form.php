<div class="form">

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm'); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->textFieldRow($model,'title',array('size'=>80,'maxlength'=>128)); ?>

	<?php echo $form->textAreaRow($model,'content',array(
		'class' => 'span8',
		'rows' => 10,
		'hint' => 'You may use <a target="_blank" href="http://daringfireball.net/projects/markdown/syntax">Markdown syntax</a>.',
	)); ?>

	<?php echo $form->textFieldRow($model,'tags', array(
		'hint'=>'Please separate different tags with commas.',
	)); ?>

	<?php echo $form->dropDownListRow($model,'status',Lookup::items('PostStatus')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType' => 'submit',
			'type'=>'primary',
			'label' => $model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->