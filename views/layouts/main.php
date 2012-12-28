<?php /* @var $this AuthController */ ?>

<?php $this->beginContent($this->module->appLayout); ?>

<div class="auth-module">

	<?php $this->widget('bootstrap.widgets.TbMenu', array(
		'type' => 'tabs',
		'items' => array(
			array(
				'label' => Yii::t('AuthModule.main', 'Assignments'),
				'url' => array('/auth/assignment/index'),
			),
			array(
				'label' => ucfirst($this->getItemTypeText(CAuthItem::TYPE_ROLE)),
				'url' => array('/auth/authItem/index', 'type' => CAuthItem::TYPE_ROLE),
			),
			array(
				'label' => ucfirst($this->getItemTypeText(CAuthItem::TYPE_TASK)),
				'url' => array('/auth/authItem/index', 'type' => CAuthItem::TYPE_TASK),
			),
			array(
				'label' => ucfirst($this->getItemTypeText(CAuthItem::TYPE_OPERATION)),
				'url' => array('/auth/authItem/index', 'type' => CAuthItem::TYPE_OPERATION),
			),
		),
	));?>

	<?php echo $content; ?>

</div>

<?php $this->endContent(); ?>