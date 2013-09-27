<?php
/**
 * AuthItemDataProvider class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.components
 */

/**
 * Data provider for listing authorization items.
 */
class AuthItemDataProvider extends CDataProvider
{
    /**
     * @var string the item type (0=operation, 1=task, 2=role).
     */
    public $type;

    private $_items = array();

    /**
     * Sets the authorization items.
     * @param CAuthItem[] $authItems the items.
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
        if (empty($this->_items) && $this->type !== null) {
            $authItems = Yii::app()->authManager->getAuthItems($this->type);
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
