<?php
$this->breadcrumbs=array(
	'New post',
);
?>
<h1>New post</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>