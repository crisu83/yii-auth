<?php
/* @var $this AuthController */
?>

<div class="auth-module">

    <?php $this->widget(
        'bootstrap.widgets.TbNav',
        [
            'type' => TbHtml::NAV_TYPE_TABS,
            'items' => [
                [
                    'label' => Yii::t('AuthModule.main', 'Assignments'),
                    'url' => ['/auth/assignment/index'],
                    'active' => $this instanceof AssignmentController,
                ],
                [
                    'label' => $this->capitalize($this->getItemTypeText(CAuthItem::TYPE_ROLE, true)),
                    'url' => ['/auth/role/index'],
                    'active' => $this instanceof RoleController,
                ],
                [
                    'label' => $this->capitalize($this->getItemTypeText(CAuthItem::TYPE_TASK, true)),
                    'url' => ['/auth/task/index'],
                    'active' => $this instanceof TaskController,
                ],
                [
                    'label' => $this->capitalize($this->getItemTypeText(CAuthItem::TYPE_OPERATION, true)),
                    'url' => ['/auth/operation/index'],
                    'active' => $this instanceof OperationController,
                ],
            ],
        ]
    );?>

    <?php echo $content; ?>

</div>
