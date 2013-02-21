<?php
/**
 * SingleDbAuthManager class file.
 * @author Jan Was <jwas@nets.com.pl>
 * @copyright Copyright &copy; Jan Was 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.components
 */

Yii::import('auth.components.CachedDbAuthManager');

/**
 * Read whole auth tree at once and cache it.
 */
class SingleDbAuthManager extends CachedDbAuthManager
{
	/**
	 * @var array @see loadItems(), contains: itemName => array(
	 *                item => AuthItem,
	 *                parents => array of reference to _items,
	 *                children => array of reference to _items
	 *            )
	 */
	protected $_items;
	/**
	 * @var array @see loadTypes(), contains: type => array(itemName => reference to _items)
	 */
	protected $_types;
	/**
	 * @var array @see loadAssignments(), contains: userId, itemName => array(item => reference to _items, assignment => CAuthAssignment)
	 */
	protected $_assignments;

	/**
	 * Returns all auth items in an indexed structure (self-referenced).
	 * @return array build of: itemName => array(
	 *                item => AuthItem,
	 *                parents => array of reference to _items,
	 *                children => array of reference to _items
	 *            )
	 */
	protected function loadItems()
	{
		if ($this->_items !== null)
			return $this->_items;

		$items=$this->db->createCommand()->select('*')->from($this->itemTable)->queryAll();
		$relations=$this->db->createCommand()->select('*')->from($this->itemChildTable)->queryAll();

		$this->_items = array();

		// first extract data avoiding slow @ operator on unserialize
		// to supress a E_NOTICE issued when $item['data'] cannot be unserialized
		$itemData = array();
		$errorReporting = error_reporting();
		error_reporting($errorReporting ^ E_NOTICE);
		foreach($items as $item)
		{
			// take extra caution not to generate any E_NOTICE, because it's suppressed
			if (!isset($item['data']) || !isset($item['name']) || ($itemData[$item['name']]=unserialize($item['data']))===false)
				$itemData[$item['name']]=null;
		}
		error_reporting($errorReporting);
		// create auth items and index them by type
		foreach($items as $item)
		{
			$this->_items[$item['name']]=array(
				'item'=>new CAuthItem($this,
					$item['name'],$item['type'],$item['description'],
					$item['bizrule'],$itemData[$item['name']]
				),
				'children' => array(),
				'parents' => array(),
			);
			if (!isset($this->_types[$item['type']]))
				$this->_types[$item['type']] = array();
			$this->_types[$item['type']][$item['name']] = &$this->_items[$item['name']];
		}

		// create parent-children references
		foreach($relations as $relation)
		{
			$this->_items[$relation['parent']]['children'][$relation['child']] = &$this->_items[$relation['child']];
			$this->_items[$relation['child']]['parents'][$relation['parent']] = &$this->_items[$relation['parent']];
		}
		return $this->_items;
	}

	/**
	 * Returns all user assignments in an indexed structure.
	 * @return array build of: userId, itemName => array(item => reference to _items, assignment => CAuthAssignment)
	 */
	protected function loadUsers()
	{
		if ($this->_assignments !== null)
			return $this->_assignments;

		$this->loadItems();

		$users=$this->db->createCommand()->select('*')->from($this->assignmentTable)->queryAll();

		$this->_assignments = array();

		// first extract data avoiding slow @ operator on unserialize
		// to supress a E_NOTICE issued when $item['data'] cannot be unserialized
		$userData = array();
		$errorReporting = error_reporting();
		error_reporting($errorReporting ^ E_NOTICE);
		foreach($users as $user)
		{
			// take extra caution not to generate any E_NOTICE, because it's suppressed
			if (!isset($userData[$user['userid']]))
				$userData[$user['userid']] = array();
			if(!isset($user['userid']) || !isset($user['itemname']) || !isset($user['data'])
				|| ($userData[$user['userid']][$user['itemname']]=unserialize($user['data']))===false)
			{
				$userData[$user['userid']][$user['itemname']]=null;
			}
		}
		error_reporting($errorReporting);

		// index by user
		foreach($users as $user)
		{
			if (!isset($this->_assignments[$user['userid']]))
				$this->_assignments[$user['userid']] = array();
			$this->_assignments[$user['userid']][$user['itemname']] = array(
				'item'=>&$this->_items[$user['itemname']],
				'assignment'=>new CAuthAssignment($this,$user['itemname'],$user['userid'],$user['bizrule'],$userData[$user['userid']][$user['itemname']]),
			);
		}
		return $this->_assignments;
	}

	/**
	 * Performs access check for the specified user.
	 * This method is internally called by {@link checkAccess}.
	 * @param mixed $itemName the name of the operation or an array of 'item','children','parents' with auth item that need access check
	 * @param mixed $userId the user ID. This should can be either an integer and a string representing
	 * the unique identifier of a user. See {@link IWebUser::getId}.
	 * @param array $params name-value pairs that would be passed to biz rules associated
	 * with the tasks and roles assigned to the user.
	 * Since version 1.1.11 a param with name 'userId' is added to this array, which holds the value of <code>$userId</code>.
	 * @param array $assignments the assignments to the specified user
	 * @return boolean whether the operations can be performed by the user.
	 * @since 1.1.3
	 */
	protected function checkAccessRecursive($itemName,$userId,$params,$assignments)
	{
		if (is_array($itemName))
		{
			$itemEx = $itemName;
			$itemName = $itemEx['item']->name;
		}
		elseif($itemName===null || ($itemEx=$this->getAuthItemEx($itemName))===null)
		{
			return false;
		}
		$item = &$itemEx['item'];
		Yii::trace('Checking permission "'.$item->getName().'"','NetCachedDbAuthManager');
		if(!isset($params['userId']))
		    $params['userId'] = $userId;
		if($this->executeBizRule($item->getBizRule(),$params,$item->getData()))
		{
			if(in_array($itemName,$this->defaultRoles))
				return true;
			if(isset($assignments[$itemName]))
			{
				$assignment=$assignments[$itemName];
				if($this->executeBizRule($assignment->getBizRule(),$params,$assignment->getData()))
					return true;
			}
			foreach($itemEx['parents'] as $parent)
			{
				if($this->checkAccessRecursive($parent,$userId,$params,$assignments))
					return true;
			}
		}
		return false;
	}

	/**
	 * Adds an item as a child of another item.
	 * @param string $itemName the parent item name
	 * @param string $childName the child item name
	 * @return boolean whether the item is added successfully
	 * @throws CException if either parent or child doesn't exist or if a loop has been detected.
	 */
	public function addItemChild($itemName,$childName)
	{
		if($itemName===$childName)
			throw new CException(Yii::t('yii','Cannot add "{name}" as a child of itself.',
					array('{name}'=>$itemName)));

		$items = $this->loadItems();
		if (!isset($items[$itemName]) || !isset($items[$childName]))
			throw new CException(Yii::t('yii','Either "{parent}" or "{child}" does not exist.',array('{child}'=>$childName,'{parent}'=>$itemName)));

		$parent = $items[$itemName];
		$child = $items[$childName];

		$this->checkItemChildType($parent['item']->type,$child['item']->type);
		if($this->detectLoopEx($parent,$child))
			throw new CException(Yii::t('yii','Cannot add "{child}" as a child of "{name}". A loop has been detected.',
				array('{child}'=>$childName,'{name}'=>$itemName)));

		$this->db->createCommand()
			->insert($this->itemChildTable, array(
				'parent'=>$itemName,
				'child'=>$childName,
			));
		$this->_items[$itemName]['children'][$childName] = &$this->_items[$childName];
		$this->_items[$childName]['parents'][$itemName] = &$this->_items[$itemName];

		return true;
	}

	/**
	 * Removes a child from its parent.
	 * Note, the child item is not deleted. Only the parent-child relationship is removed.
	 * @param string $itemName the parent item name
	 * @param string $childName the child item name
	 * @return boolean whether the removal is successful
	 */
	public function removeItemChild($itemName,$childName)
	{
		$this->loadItems();
		unset($this->_items[$itemName]['children'][$childName]);
		unset($this->_items[$childName]['parents'][$itemName]);
		return $this->db->createCommand()
			->delete($this->itemChildTable, 'parent=:parent AND child=:child', array(
				':parent'=>$itemName,
				':child'=>$childName
			)) > 0;
	}

	/**
	 * Returns a value indicating whether a child exists within a parent.
	 * @param string $itemName the parent item name
	 * @param string $childName the child item name
	 * @return boolean whether the child exists
	 */
	public function hasItemChild($itemName,$childName)
	{
		$items = $this->loadItems();
		return isset($items[$itemName]) && isset($items[$itemName]['children'][$childName]);
	}

	/**
	 * Returns the children of the specified item.
	 * @param mixed $names the parent item name. This can be either a string or an array.
	 * The latter represents a list of item names.
	 * @return array all child items of the parent
	 */
	public function getItemChildren($names)
	{
		$items = $this->loadItems();
		$children = array();
		if(is_string($names)) {
			$children = !isset($items[$names]) ? array() : $items[$names]['children'];
		}
		elseif(is_array($names) && $names!==array())
		{
			$children = array();
			foreach($names as &$name)
			{
				if (isset($items[$name]))
				{
					$children = array_merge($children, $items[$name]['children']);
				}
			}
		}

		return array_map(function($i){return $i['item'];}, $children);
	}

	/**
	 * Assigns an authorization item to a user.
	 * @param string $itemName the item name
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @param string $bizRule the business rule to be executed when {@link checkAccess} is called
	 * for this particular authorization item.
	 * @param mixed $data additional data associated with this assignment
	 * @return CAuthAssignment the authorization assignment information.
	 * @throws CException if the item does not exist or if the item has already been assigned to the user
	 */
	public function assign($itemName,$userId,$bizRule=null,$data=null)
	{
		$result = parent::assign($itemName, $userId, $bizRule, $data);
		$items = $this->loadItems();
		$users = $this->loadUsers();
		if (!isset($this->_assignments[$userId]))
			$this->_assignments[$userId] = array();
		$this->_assignments[$userId][$itemName] = array(
			'item'=>&$this->_items[$itemName],
			'assignment'=>new CAuthAssignment($this,$itemName,$userId,$bizRule,$data),
		);
		return $result;
	}

	/**
	 * Revokes an authorization assignment from a user.
	 * @param string $itemName the item name
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @return boolean whether removal is successful
	 */
	public function revoke($itemName,$userId)
	{
		$result = parent::revoke($itemName, $userId);
		$users = $this->loadUsers();
		if (isset($this->_assignments[$userId]) && isset($this->_assignments[$userId][$itemName]))
			unset($this->_assignments[$userId][$itemName]);
		return $result;
	}

	/**
	 * Returns a value indicating whether the item has been assigned to the user.
	 * @param string $itemName the item name
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @return boolean whether the item has been assigned to the user.
	 */
	public function isAssigned($itemName,$userId)
	{
		$users = $this->loadUsers();
		return isset($users[$userId]) && isset($users[$userId][$itemName]);
	}

	/**
	 * Returns the item assignment information.
	 * @param string $itemName the item name
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @return CAuthAssignment the item assignment information. Null is returned if
	 * the item is not assigned to the user.
	 */
	public function getAuthAssignment($itemName,$userId)
	{
		$users = $this->loadUsers();
		if (!isset($users[$userId]) || !isset($users[$userId][$itemName]))
			return null;

		return $users[$userId][$itemName]['assignment'];
	}

	/**
	 * Returns the item assignments for the specified user.
	 * @param mixed $userId the user ID (see {@link IWebUser::getId})
	 * @return array the item assignment information for the user. An empty array will be
	 * returned if there is no item assigned to the user.
	 */
	public function getAuthAssignments($userId)
	{
		$users = $this->loadUsers();
		if (!isset($users[$userId]))
			return array();
		$assignments=array();
		foreach($users[$userId] as $itemName=>$row)
		{
			$assignments[$itemName]=$row['assignment'];
		}
		return $assignments;
	}

	/**
	 * Saves the changes to an authorization assignment.
	 * @param CAuthAssignment $assignment the assignment that has been changed.
	 */
	public function saveAuthAssignment($assignment)
	{
		parent::saveAuthAssignment($assignment);
		$users = $this->loadUsers();
		$this->_assignments[$assignment->getUserId()][$assignment->getItemName()]['assignment'] = $assignment;
	}

	/**
	 * Returns the authorization items of the specific type and user.
	 * @param integer $type the item type (0: operation, 1: task, 2: role). Defaults to null,
	 * meaning returning all items regardless of their type.
	 * @param mixed $userId the user ID. Defaults to null, meaning returning all items even if
	 * they are not assigned to a user.
	 * @return array the authorization items of the specific type.
	 */
	public function getAuthItems($type=null,$userId=null)
	{
		$items = $this->loadItems();
		$users = $this->loadUsers();
		if($type===null && $userId===null)
		{
			return array_map(function($i){return $i['item'];}, $items);
		}
		elseif($userId===null)
		{
			if (!isset($this->_types[$type]))
				return array();
			return array_map(function($i){return $i['item'];}, $this->_types[$type]);
		}

		if (!isset($users[$userId]))
			return array();
		if ($type !== null)
		{
			if (!isset($this->_types[$type]))
				return array();
			$userItems = array_filter($users[$userId], function($a)use($type){return $type==$a['item']['item']->type;});
		}
		else
		{
			$userItems = $users[$userId];
		}
		return array_map(function($a) {
			$item = clone $a['item']['item'];
			$item->setBizRule($a['assignment']->bizRule);
			$item->setData($a['assignment']->data);
			return $item;
		}, $userItems);
	}

	/**
	 * Creates an authorization item.
	 * An authorization item represents an action permission (e.g. creating a post).
	 * It has three types: operation, task and role.
	 * Authorization items form a hierarchy. Higher level items inheirt permissions representing
	 * by lower level items.
	 * @param string $name the item name. This must be a unique identifier.
	 * @param integer $type the item type (0: operation, 1: task, 2: role).
	 * @param string $description description of the item
	 * @param string $bizRule business rule associated with the item. This is a piece of
	 * PHP code that will be executed when {@link checkAccess} is called for the item.
	 * @param mixed $data additional data associated with the item.
	 * @return CAuthItem the authorization item
	 * @throws CException if an item with the same name already exists
	 */
	public function createAuthItem($name,$type,$description='',$bizRule=null,$data=null)
	{
		$result = parent::createAuthItem($name, $type, $description, $bizRule, $data);
		$items = $this->loadItems();
		$this->_items[$name] = array(
			'item'=>$result,
			'children'=>array(),
			'parents'=>array(),
		);
		$this->_types[$type][$name] = &$this->_items[$name];
		return $result;
	}

	/**
	 * Removes the specified authorization item.
	 * @param string $name the name of the item to be removed
	 * @return boolean whether the item exists in the storage and has been removed
	 */
	public function removeAuthItem($name)
	{
		$items = $this->loadItems();
		$users = $this->loadUsers();
		if (isset($items[$name]))
		{
			unset($this->_types[$items[$name]['item']->type][$name]);
			unset($this->_items[$name]);
		}
		foreach($this->_assignments as $userId=>$item)
		{
			if (isset($this->_assignments[$userId][$name]))
				unset($this->_assignments[$userId][$name]);
		}
		return parent::removeAuthItem($name);
	}

	/**
	 * Returns the authorization item with the specified name.
	 * @param string $name the name of the item
	 * @return CAuthItem the authorization item. Null if the item cannot be found.
	 */
	public function getAuthItem($name)
	{
		$items = $this->loadItems();
		return !isset($items[$name]) ? null : $items[$name]['item'];
	}

	/**
	 * Returns an array with 'item' key holding the authorization item with specified name,
	 * 'parents' and 'children' holding references to other items.
	 */
	public function getAuthItemEx($name)
	{
		$items = $this->loadItems();
		return !isset($items[$name]) ? null : $items[$name];
	}

	/**
	 * Saves an authorization item to persistent storage.
	 * @param CAuthItem $item the item to be saved.
	 * @param string $oldName the old item name. If null, it means the item name is not changed.
	 */
	public function saveAuthItem($item,$oldName=null)
	{
		$items = $this->loadItems();
		$users = $this->loadUsers();
		if ($oldName === null || $item->getName()===$oldName)
		{
			// name has not changed
			$this->_items[$name]->type = $item->type;
			$this->_items[$name]->description = $item->description;
			$this->_items[$name]->bizRule = $item->bizRule;
			$this->_items[$name]->data = $item->data;
		}
		else
		{
			// name has changed
			$oldItem = $this->_items[$oldName];
			// remove old user associations and type index
			unset($this->_types[$oldItem['item']->type][$oldItem['item']->name]);
			$oldUsers = array();
			foreach($this->_assignments as $userId=>$userItem)
			{
				if (isset($this->_assignments[$userId][$oldName]))
				{
					$oldUsers[$userId] = $userItem;
					unset($this->_assignments[$userId][$oldName]);
				}
			}
			// remove old item, including references in parents and children
			foreach($oldItem['parents'] as $parentName => $parentItem)
			{
				unset($this->_items[$parentName]['children'][$oldName]);
			}
			foreach($oldItem['children'] as $childName => $childItem)
			{
				unset($this->_items[$childName]['parents'][$oldName]);
			}
			unset($this->_items[$oldName]);
			// add new item, including references in parents and children
			$this->_items[$item->name] = array(
				'item'=>$item,
				'children'=>$oldItem['children'],
				'parents'=>$oldItem['parents'],
			);
			foreach($oldItem['parents'] as $parentName => $parentItem)
			{
				$this->_items[$parentName]['children'][$item->name] = &$this->_items[$item->name];
			}
			foreach($oldItem['children'] as $childName => $childItem)
			{
				$this->_items[$childName]['parents'][$item->name] = &$this->_items[$item->name];
			}
			// add new user associations and type index
			$this->_types[$item->type][$item->name] = &$this->_items[$item->name];
			foreach($oldUsers as $oldUserId => $oldUserItem)
			{
				$oldUserItem['assignment']->
				$this->_assignments[$oldUserId][$item->name] = array(
					'item' => &$this->_items[$item->name],
					'assignment' => new CAuthAssignment($this,$item->name,$oldUserId,$oldUserItem['assignment']->bizRule,$oldUserItem['assignment']->data),
				);
			}
		}
		return parent::saveAuthItem($item,$oldName);
	}

	/**
	 * Saves the authorization data to persistent storage.
	 */
	public function save()
	{
	}

	/**
	 * Removes all authorization data.
	 */
	public function clearAll()
	{
		$this->clearAuthAssignments();
		$this->db->createCommand()->delete($this->itemChildTable);
		$this->db->createCommand()->delete($this->itemTable);
		$this->_items = array();
		$this->_types = array();
	}

	/**
	 * Removes all authorization assignments.
	 */
	public function clearAuthAssignments()
	{
		$this->db->createCommand()->delete($this->assignmentTable);
		$this->_assignments = array();
	}

	/**
	 * Checks whether there is a loop in the authorization item hierarchy.
	 * @param string $itemName parent item name
	 * @param string $childName the name of the child item that is to be added to the hierarchy
	 * @return boolean whether a loop exists
	 */
	protected function detectLoop($itemName,$childName)
	{
		$items = $this->loadItems();
		return $this->detectLoop($items[$itemName], $items[$childName]);
	}

	/**
	 * Checks whether there is a loop in the authorization item hierarchy.
	 * @param array $item parent item
	 * @param array $child child item that is to be added to the hierarchy
	 * @return boolean whether a loop exists
	 */
	protected function detectLoopEx($item,$child)
	{
		if($item['item']->name===$child['item']->name)
			return true;
		foreach($child['children'] as $grandchild)
		{
			if($this->detectLoopEx($item,$grandchild))
				return true;
		}
		return false;
	}
}
