<?php
/* @var $this OperationController|TaskController|RoleController */
/* @var $item CAuthItem */
/* @var $ancestorDp AuthItemDataProvider */
/* @var $descendantDp AuthItemDataProvider */
/* @var $formModel AddAuthItemForm */
/* @var $form TbActiveForm */
/* @var $childOptions array */

$this->breadcrumbs = array(
    $this->capitalize($this->getTypeText(true)) => array('index'),
    $item->description,
);
?>

    <div class="title-row clearfix">

        <h1 class="pull-left">
            <?php echo CHtml::encode($item->description); ?>
            <small><?php echo $this->getTypeText(); ?></small>
        </h1>

        <?php echo TbHtml::buttonGroup(
            [
                [
                    'icon' => 'pencil',
                    'label' => Yii::t('AuthModule.main', ''),
                    'url' => ['update', 'name' => $item->name],
                ],
                [
                    'icon' => 'trash',
                    'url' => ['delete', 'name' => $item->name],
                    'htmlOptions' => [
                        'confirm' => Yii::t('AuthModule.main', 'Are you sure you want to delete this item?'),
                    ],
                ],
            ],
            ['class' => 'pull-right']
        ); ?>

    </div>

<?php $this->widget(
    'TbDetailView',
    [
        'data' => $item,
        'attributes' => [
            [
                'name' => 'name',
                'label' => Yii::t('AuthModule.main', 'System name'),
            ],
            [
                'name' => 'description',
                'label' => Yii::t('AuthModule.main', 'Description'),
            ],
            /*
            array(
                'name' => 'bizrule',
                'label' => Yii::t('AuthModule.main', 'Business rule'),
            ),
            array(
                'name' => 'data',
                'label' => Yii::t('AuthModule.main', 'Data'),
            ),
            */
        ],
    ]
); ?>

    <hr/>

    <h3>
        <?php echo Yii::t('AuthModule.main', 'Ancestors'); ?>
        <small><?php echo Yii::t('AuthModule.main', 'Permissions that inherit this item'); ?></small>
    </h3>

<?php $this->widget(
    'bootstrap.widgets.TbGridView',
    array(
        'id' => 'auth-view-grid',
        'type' => [TbHtml::GRID_TYPE_BORDERED, TbHtml::GRID_TYPE_STRIPED],
        'dataProvider' => $ancestorDp,
        'emptyText' => Yii::t('AuthModule.main', 'This item does not have any ancestors.'),
        'template' => "{items}",
        'columns' => [
            [
                'class' => 'AuthItemDescriptionColumn',
                'itemName' => $item->name,
            ],
            [
                'class' => 'AuthItemTypeColumn',
                'itemName' => $item->name,
            ],
            [
                'class' => 'AuthItemRemoveColumn',
                'itemName' => $item->name,
            ],
        ],
    )
); ?>

    <hr>
    <h3>
        <?php echo Yii::t('AuthModule.main', 'Descendants'); ?>
        <small><?php echo Yii::t('AuthModule.main', 'Permissions granted by this item'); ?></small>
    </h3>

<?php $this->widget(
    'bootstrap.widgets.TbGridView',
    [
        'id' => 'authitem-view-grid',
        'type' => [TbHtml::GRID_TYPE_BORDERED, TbHtml::GRID_TYPE_STRIPED],
        'dataProvider' => $descendantDp,
        'emptyText' => Yii::t('AuthModule.main', 'This item does not have any descendants.'),
        'template' => "{items}",
        'columns' => [
            [
                'class' => 'AuthItemDescriptionColumn',
                'itemName' => $item->name,
            ],
            [
                'class' => 'AuthItemTypeColumn',
                'itemName' => $item->name,
            ],
            [
                'class' => 'AuthItemRemoveColumn',
                'itemName' => $item->name,
            ],
        ],
    ]
); ?>
    <hr>
<?php if (!empty($childOptions)): ?>

    <h4><?php echo Yii::t('AuthModule.main', 'Add child'); ?></h4>

    <?php $form = $this->beginWidget(
        'bootstrap.widgets.TbActiveForm',
        array(
            'layout' => TbHtml::FORM_LAYOUT_INLINE,
        )
    ); ?>

    <?php echo $form->dropDownListControlGroup($formModel, 'items', $childOptions, array('label' => false)); ?>

    <?php echo TbHtml::submitButton(
        Yii::t('AuthModule.main', 'Add'),
        array(
            'color' => TbHtml::BUTTON_COLOR_PRIMARY,
        )
    ); ?>

    <?php $this->endWidget(); ?>

<?php endif;

Yii::app()->clientScript->registerScript('authComponents', "
        $(function () {   
            // initialize dataTable
            $('#authitem-view-grid table').DataTable({
              'paging'      : true,
              'lengthChange': true,
              'searching'   : true,
              'ordering'    : true,
              'info'        : true,
              'autoWidth'   : false
            });
        })", CClientScript::POS_END);
