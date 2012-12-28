<?php

class AuthItemDataProvider extends CDataProvider
{
    public $type;

    private $_items = array();

    /**
     * @param CAuthItem[] $authItems
     */
    public function setAuthItems($authItems)
    {
        $this->_items = array_values($authItems);
    }

    /**
     * Fetches the data from the persistent data storage.
     * @return array list of data items
     */
    protected function fetchData()
    {
        if (empty($this->_items) && $this->type !== null)
        {
            $authItems = Yii::app()->authManager->loadAuthItems($this->type);
            $this->setAuthItems($authItems);
        }

        return $this->_items;
    }

    /**
     * Fetches the data item keys from the persistent data storage.
     * @return array list of data item keys.
     */
    protected function fetchKeys()
    {
        return array('name', 'description', 'type', 'bizrule', 'data');
    }

    /**
     * Calculates the total number of data items.
     * @return integer the total number of data items.
     */
    protected function calculateTotalItemCount()
    {
        return count($this->_items);
    }
}
