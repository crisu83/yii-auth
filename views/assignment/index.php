<?php
/* @var $this AssignmentController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs = array(
    Yii::t('AuthModule.main', 'Assignments'),
);
?>

<h1><?php echo Yii::t('AuthModule.main', 'Assignments'); ?></h1>

<?php $this->widget(
    'bootstrap.widgets.TbGridView',
    array(
        'type' => 'striped hover',
        'dataProvider' => $dataProvider,
        'emptyText' => Yii::t('AuthModule.main', 'No assignments found.'),
        'template' => "{items}\n{pager}",
        'filter' => $model,
        'columns' => array(
        	array(
        		'name' => 'username',
        		'header' => Yii::t('AuthModule.main', 'User'),
        		'type' => 'raw',
        		'value' => function($data, $row) {
        			return TbHtml::link(
        				$data->username,
        				'/auth/assignment/view/id/' . $data->id
        			);
        		},
        	),
            array(
                'header' => Yii::t('AuthModule.main', 'Assigned items'),
                'class' => 'AuthAssignmentItemsColumn',
            ),
            array(
                'class' => 'AuthAssignmentViewColumn',
            ),
        ),
    )
); ?>
