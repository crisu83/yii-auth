<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
	<div class="row">
		<div class="span9">
			<div id="content">
				<?php echo $content; ?>
			</div><!-- content -->
		</div>
		<div class="span3">
			<div id="sidebar">
				<?php if(!Yii::app()->user->isGuest): ?>
					<h3><?php echo CHtml::encode(Yii::app()->user->name); ?></h3>
					<?php $this->widget('bootstrap.widgets.TbMenu', array(
						'type'=>'list',
						'htmlOptions'=>array('class'=>'user-menu'),
						'items'=>array(
							array('label'=>'Create New Post', 'url'=>array('post/create')),
							array('label'=>'Manage Posts', 'url'=>array('post/admin')),
							array('label'=>'Approve Comments', 'url'=>array('comment/index')),
							array('label'=>'Logout', 'url'=>array('site/logout')),
						),
					)); ?>
				<?php endif; ?>

				<?php $this->widget('TagCloud', array(
					'maxTags'=>Yii::app()->params['tagCloudCount'],
				)); ?>

				<?php $this->widget('RecentComments', array(
					'maxComments'=>Yii::app()->params['recentCommentCount'],
				)); ?>
			</div><!-- sidebar -->
		</div>
	</div>
<?php $this->endContent(); ?>