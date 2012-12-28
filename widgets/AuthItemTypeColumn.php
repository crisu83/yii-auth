<?php

Yii::import('zii.widgets.grid.CGridColumn');

class AuthItemTypeColumn extends AuthItemColumn
{
    /**
     * Initializes the column.
     */
    public function init()
    {
        if (isset($this->htmlOptions['class']))
            $this->htmlOptions['class'] .= ' auth-item-type-column';
        else
            $this->htmlOptions['class'] = 'auth-item-type-column';
    }

    /**
     * Renders the data cell content.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data associated with the row
     */
    protected function renderDataCellContent($row, $data)
    {
        /* @var $am CAuthManager|AuthBehavior */
        $am = Yii::app()->getAuthManager();

        $labelType = $this->active || $am->hasParent($this->itemName, $data['name']) || $am->hasChild($this->itemName, $data['name'])
            ? 'info'
            : '';

        /* @var $controller AuthItemController */
        $controller = $this->grid->getOwner();
        $controller->widget('bootstrap.widgets.TbLabel', array(
            'type' => $labelType,
            'label' => $controller->getItemTypeText($data['item']->type, false),
        ));
    }
}
