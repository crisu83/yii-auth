<?php

class AuthAssignmentNameColumn extends AuthAssignmentColumn
{
    /**
     * @var string
     */
    public $nameColumn;

    /**
     * Initializes the column.
     */
    public function init()
    {
        if (isset($this->htmlOptions['class']))
            $this->htmlOptions['class'] .= ' name-column';
        else
            $this->htmlOptions['class'] = 'name-column';
    }

    /**
     * Renders the data cell content.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data associated with the row
     */
    protected function renderDataCellContent($row, $data)
    {
        if (isset($this->nameColumn))
            echo $data->{$this->nameColumn};
    }
}
