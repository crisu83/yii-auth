<?php
/* @var $this OperationController|TaskController|RoleController */
/* @var $dataProvider AuthItemDataProvider */

$this->breadcrumbs = array(
    $this->capitalize($this->getTypeText(true)),
);
?>

    <h1><?php echo $this->capitalize($this->getTypeText(true)); ?></h1>

<?php echo TbHtml::linkButton(
    Yii::t('AuthModule.main', 'Add {type}', array('{type}' => $this->getTypeText())),
    array(
        'color' => TbHtml::BUTTON_COLOR_PRIMARY,
        'url' => array('create'),
    )
); ?>
    <hr>
<?php $this->widget(
    'bootstrap.widgets.TbGridView',
    [
        'type' => [TbHtml::GRID_TYPE_BORDERED, TbHtml::GRID_TYPE_STRIPED],
        'id' => 'task-grid',
        'dataProvider' => $dataProvider,
        'emptyText' => Yii::t('AuthModule.main', 'No {type} found.', ['{type}' => $this->getTypeText(true)]),
        'template' => "{items}",
        'columns' => [
            [
                'name' => 'name',
                'type' => 'raw',
                'header' => Yii::t('AuthModule.main', 'System name'),
                'htmlOptions' => ['class' => 'item-name-column'],
                'value' => "CHtml::link(\$data->name, array('view', 'name'=>\$data->name))",
            ],
            [
                'name' => 'description',
                'header' => Yii::t('AuthModule.main', 'Description'),
                'htmlOptions' => ['class' => 'item-description-column'],
            ],
            [
                'class' => 'TbButtonColumn',
                'template' => '{view} {update} {delete}',
                'viewButtonOptions' => ['title' => 'View'],
                'viewButtonIcon' => false,
                'viewButtonImageUrl' => false,
                'viewButtonLabel' => '<button class="btn btn-xs btn-primary"><i class="fa fa-info-circle"></i></button>',
                'viewButtonUrl' => "Yii::app()->controller->createUrl('view', array('name'=>\$data->name))",

                'updateButtonOptions' => ['title' => 'Update'],
                'updateButtonIcon' => false,
                'updateButtonImageUrl' => false,
                'updateButtonLabel' => Yii::t('AuthModule.main', '<button class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></button>'),
                'updateButtonUrl' => "Yii::app()->controller->createUrl('update', array('name'=>\$data->name))",

                'deleteButtonOptions' => ['title' => 'Delete'],
                'deleteButtonIcon' => false,
                'deleteButtonImageUrl' => false,
                'deleteButtonLabel' => Yii::t('AuthModule.main', '<button class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>'),
                'deleteButtonUrl' => "Yii::app()->controller->createUrl('delete', array('name'=>\$data->name))",
                'deleteConfirmation' => Yii::t('AuthModule.main', 'Are you sure you want to delete this item?'),
            ],
        ],
    ]
);

Yii::app()->clientScript->registerScript('streamProfileComponents', "
        $(function () {
                 
            // initialize dataTable
            $('#task-grid table').DataTable({
              'paging'      : true,
              'lengthChange': true,
              'searching'   : true,
              'ordering'    : true,
              'info'        : true,
              'autoWidth'   : false
            });
                 
        })", CClientScript::POS_END);
