<?php
/* @var $this AssignmentController */
/* @var $model User */
/* @var $authItemDp AuthItemDataProvider */
/* @var $formModel AddAuthItemForm */
/* @var $form TbActiveForm */
/* @var $assignmentOptions array */

$this->breadcrumbs = [
    Yii::t('AuthModule.main', 'Assignments') => ['index'],
    TbHtml::value($model, $this->module->userNameColumn),
];
?>

    <h1><?php echo TbHtml::encode(TbHtml::value($model, $this->module->userNameColumn)); ?>
        <small><?php echo Yii::t('AuthModule.main', 'Assignments'); ?></small>
    </h1>


    <h3>
        <?php echo Yii::t('AuthModule.main', 'Permissions'); ?>
        <small><?php echo Yii::t('AuthModule.main', 'Items assigned to this user'); ?></small>
    </h3>

<?php $this->widget(
    'TbGridView',
    [
        'id' => 'auth-view-grid',
        'type' => [TbHtml::GRID_TYPE_BORDERED, TbHtml::GRID_TYPE_STRIPED],
        'dataProvider' => $authItemDp,
        'emptyText' => Yii::t('AuthModule.main', 'This user does not have any assignments.'),
        'template' => "{items}",
        'columns' => [
            [
                'class' => 'AuthItemDescriptionColumn',
                'active' => true,
            ],
            [
                'class' => 'AuthItemTypeColumn',
                'active' => true,
            ],
            [
                'class' => 'AuthAssignmentRevokeColumn',
                'userId' => $model->{$this->module->userIdColumn},
            ],
        ],
    ]
); ?>

<?php if (!empty($assignmentOptions)): ?>

    <h4><?php echo Yii::t('AuthModule.main', 'Assign permission'); ?></h4>

    <?php $form = $this->beginWidget(
        'bootstrap.widgets.TbActiveForm',
        array(
            'layout' => TbHtml::FORM_LAYOUT_INLINE,
        )
    ); ?>

    <?php echo $form->dropDownList($formModel, 'items', $assignmentOptions, array('label' => false)); ?>

    <?php echo TbHtml::submitButton(
        Yii::t('AuthModule.main', 'Assign'),
        array(
            'color' => TbHtml::BUTTON_COLOR_PRIMARY,
        )
    ); ?>

    <?php $this->endWidget(); ?>

<?php endif; ?>
<?php

Yii::app()->clientScript->registerScript('authComponents', "
        $(function () {   
            // initialize dataTable
            $('#auth-view-grid table').DataTable({
              'paging'      : true,
              'lengthChange': true,
              'searching'   : true,
              'ordering'    : true,
              'info'        : true,
              'autoWidth'   : false
            });
        })", CClientScript::POS_END);

