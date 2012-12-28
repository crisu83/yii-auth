<?php

class AuthAssignmentItemsColumn extends AuthAssignmentColumn
{
    /**
     * Initializes the column.
     */
    public function init()
    {
        if (isset($this->htmlOptions['class']))
            $this->htmlOptions['class'] .= ' assignment-items-column';
        else
            $this->htmlOptions['class'] = 'assignment-items-column';
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
        $assignments = $am->loadAuthAssignments($data->id);
        $permissions = $am->getItemsPermissions(array_keys($assignments));
        foreach ($permissions as $itemPermission)
            echo $itemPermission['item']->getDescription() . '<br />';
    }
}
