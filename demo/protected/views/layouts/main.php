<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/styles.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl; ?>/css/main.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

	<?php Yii::app()->bootstrap->register(); ?>
</head>

<body>

<?php $this->widget('bootstrap.widgets.TbNavbar',array(
	'type'=>'inverse',
	'items'=>array(
		array(
			'class'=>'bootstrap.widgets.TbMenu',
			'items'=>array(
				array('label'=>'Download', 'url'=>'http://www.yiiframework.com/extension/auth/'),
				array('label'=>'Fork on GitHub', 'url'=>'https://github.com/Crisu83/yii-auth/'),
				array('label'=>'Reset demo', 'url'=>array('/site/reset'), 'linkOptions'=>array('confirm'=>'Are you sure you want to reset the database?')),
			),
		),
		array(
			'class'=>'LanguageMenu',
			'htmlOptions'=>array('class'=>'pull-right'),
		),
	),
)); ?>

<div class="container" id="page">

	<?php $this->widget('bootstrap.widgets.TbAlert'); ?>

	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?>
	<?php endif?>

	<?php echo $content; ?>

	<hr />

	<div id="footer">
		&copy; Christoffer Niska <?php echo date('Y'); ?> <br/>
		All Rights Reserved.<br/>
		<?php echo Yii::powered(); ?>
	</div>

</div>

</body>
</html>
