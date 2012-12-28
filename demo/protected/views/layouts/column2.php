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
					<?php $this->widget('UserMenu'); ?>
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