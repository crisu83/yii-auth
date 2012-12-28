<?php

class AuthAssignmentRevokeColumn extends AuthAssignmentColumn
{
    /**
     * Initializes the column.
     */
    public function init()
    {
        if (isset($this->htmlOptions['class']))
            $this->htmlOptions['class'] .= ' auth-actions-column';
        else
            $this->htmlOptions['class'] = 'auth-actions-column';
    }

    /**
     * Renders the data cell content.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data associated with the row
     */
    protected function renderDataCellContent($row, $data)
    {
        if ($this->userId !== null)
        {
            $this->grid->owner->widget('bootstrap.widgets.TbButton', array(
                'type' => 'link',
                'size' => 'mini',
                'icon' => 'remove',
                'url' => array('revoke', 'itemName' => $data['name'], 'userId' => $this->userId),
                'htmlOptions' => array('rel' => 'tooltip', 'title' => Yii::t('AuthModule.main', 'Revoke')),
            ));
        }
    }
}
