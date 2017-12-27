<?php
/* @var $this AssignmentController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs = [
    Yii::t('AuthModule.main', 'Assignments'),
];

$this->pageTitle = Yii::t('AuthModule.main', 'Assignments');
?>

<?php $this->widget(
    'bootstrap.widgets.TbGridView',
    [
        'id' => 'auth-grid',
        'type' => [TbHtml::GRID_TYPE_BORDERED, TbHtml::GRID_TYPE_STRIPED],
        'dataProvider' => $dataProvider,
        'emptyText' => Yii::t('AuthModule.main', 'No assignments found.'),
        'template' => "{items}",
        'columns' => [
            [
                'header' => Yii::t('AuthModule.main', 'User'),
                'class' => 'AuthAssignmentNameColumn',
            ],
            [
                'header' => Yii::t('AuthModule.main', 'Assigned items'),
                'class' => 'AuthAssignmentItemsColumn',
            ],
            [
                'class' => 'AuthAssignmentViewColumn',
            ],
        ],
    ]
);

Yii::app()->clientScript->registerScript('authComponents', "
        $(function () {   
            // initialize dataTable
            $('#auth-grid table').DataTable({
              'paging'      : true,
              'lengthChange': true,
              'searching'   : true,
              'ordering'    : true,
              'info'        : true,
              'autoWidth'   : false
            });
        })", CClientScript::POS_END);
