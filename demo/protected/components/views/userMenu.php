<ul>
	<li><?php echo CHtml::link('Create post',array('/post/create')); ?></li>
	<li><?php echo CHtml::link('Manage posts',array('/post/admin')); ?></li>
	<li><?php echo CHtml::link('Approve comments',array('/comment/index')) . ' <span class="badge">' . Comment::model()->pendingCommentCount . '</span>'; ?></li>
	<li><?php echo CHtml::link('Manage permissions', array('/auth')); ?> <span class="label label-inverse">New</span></li>
	<?php /*<li><?php echo CHtml::link('Reset database', array('/site/reset'), array('confirm'=>'Are you sure you want to reset the database?')); ?></li>*/ ?>
	<li><?php echo CHtml::link('Logout',array('/site/logout')); ?></li>
</ul>