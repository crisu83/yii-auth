<?php
/**
 * CachedAccessRecord class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.components
 */

/**
 * Component for caching access to operations for a specific user.
 */
class CachedAccessRecord extends CComponent
{
	/**
	 * @var string name of the operation.
	 */
	public $itemName;
	/**
	 * @var integer the user id.
	 */
	public $userId;

	private $_entries = array();

	/**
	 * Creates the record.
	 * @param string $itemName name of the operation.
	 * @param integer $userId the user id.
	 */
	function __construct($itemName, $userId)
	{
		$this->itemName = $itemName;
		$this->userId = $userId;
	}

	/**
	 * Adds an entry to the record
	 * @param boolean $allow whether the user is allowed access.
	 * @param array $params name-value pairs that would be passed to biz rules associated
	 * with the tasks and roles assigned to the user.
	 */
	public function addEntry($allow, $params = array())
	{
		$this->_entries[serialize($params)] = $allow;
	}

	/**
	 * Returns whether the user has access to the operation with the given parameters.
	 * @param array $params name-value pairs that would be passed to biz rules associated
	 * with the tasks and roles assigned to the user.
	 * @return boolean the result.
	 */
	public function checkAccess($params)
	{
		$key = serialize($params);
		return isset($this->_entries[$key]) ? $this->_entries[$key] : false;
	}
}
