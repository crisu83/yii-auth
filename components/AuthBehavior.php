<?php

/**
 * @property CAuthManager $owner
 */
class AuthBehavior extends CBehavior
{
	const CACHE_KEY_PREFIX = 'AuthModule.PermissionBehavior.';

	public $cachingDuration = 0;
	public $cacheID = 'cache';

	/**
	 * @param string $itemName
	 * @param string $childName
	 * @return boolean
	 */
	public function hasPermission($itemName, $childName)
	{
		$itemPermissions = $this->getDescendants($itemName);
		return isset($itemPermissions[$childName]);
	}

	/**
	 * @param string $itemName
	 * @param array|null $items
	 * @return array
	 */
	public function getAncestors($itemName, $items = null)
	{
		$ancestors = array();

		if ($items === null)
			$items = $this->getPermissions();

		foreach ($items as $childName => $childPermissions)
		{
			if (isset($childPermissions[$itemName]))
				$ancestors[$childName] = $childName;

			$childAncestors = $this->getAncestors($itemName, $childPermissions);
			if (!empty($childAncestors))
				$ancestors = array_merge($ancestors, $childAncestors);
		}

		return array_unique($ancestors);
	}

	/**
	 * @param string $itemName
	 * @return array
	 */
	public function getDescendants($itemName)
	{
		$itemPermissions = $this->getItemPermissions($itemName);
		return $this->flattenTree($itemPermissions);
	}

	/**
	 * @param string $itemName
	 * @return array
	 */
	public function getRelatives($itemName)
	{
		$ancestors = $this->getAncestors($itemName);
		$descendants = $this->getDescendants($itemName);
		return array_merge($ancestors, $descendants);
	}

	/**
	 * @param boolean $allowCaching
	 * @return array
	 */
	public function getPermissions($allowCaching = true)
	{
		$key = self::CACHE_KEY_PREFIX . 'permissions';

		/* @var $cache CCache */
		if ($allowCaching && $this->cachingDuration > 0 && $this->cacheID !== false
				&& ($cache = Yii::app()->getComponent($this->cacheID)) !== null)
		{
			if (($data = $cache->get($key)) !== false)
				return unserialize($data);
		}

		$permissions = $this->buildPermissions();

		if (isset($cache))
			$cache->set($key, serialize($permissions), $this->cachingDuration);

		return $permissions;
	}

	/**
	 * @return array
	 */
	private function buildPermissions()
	{
		$permissions = array();
		foreach ($this->loadAuthItems(CAuthItem::TYPE_ROLE) as $roleName => $role)
			$permissions[$roleName] = $this->getItemPermissions($roleName);
		return $permissions;
	}

	/**
	 * @param string $itemName
	 * @param boolean $allowCaching
	 * @return array
	 */
	public function getItemPermissions($itemName, $allowCaching = true)
	{
		$key = self::CACHE_KEY_PREFIX . 'itemPermissions.' . $itemName;

		/* @var $cache CCache */
		if ($allowCaching && $this->cachingDuration > 0 && $this->cacheID !== false
				&& ($cache = Yii::app()->getComponent($this->cacheID)) !== null)
		{
			if (($data = $cache->get($key)) !== false)
				return unserialize($data);
		}

		$permissions = $this->buildItemPermissions($itemName);

		if (isset($cache))
			$cache->set($key, serialize($permissions), $this->cachingDuration);

		return $permissions;
	}

	/**
	 * @param string $itemName
	 * @return array
	 */
	private function buildItemPermissions($itemName)
	{
		$permissions = array();
		$item = $this->owner->getAuthItem($itemName);
		if ($item instanceof CAuthItem)
		{
			foreach ($item->getChildren() as $childName => $child)
				$permissions[$childName] = $this->getItemPermissions($childName);
		}
		return $permissions;
	}

	/**
	 * @param array $tree
	 * @return array
	 */
	public function flattenTree($tree)
	{
		$flatTree = array();
		foreach ($tree as $branchName => $branch)
		{
			$flatTree[$branchName] = $branchName;
			$leaves = $this->flattenTree($branch);
			foreach ($leaves as $leafName => $leaf)
				$flatTree[$leafName] = $leafName;
		}
		return $flatTree;
	}

	/**
	 * @param $names
	 * @return CAuthItem[]
	 */
	public function getAuthItemsByNames($names)
	{
		$authItems = array();
		foreach ($this->loadAuthItems() as $itemName => $item)
		{
			if (in_array($itemName, $names))
				$authItems[$itemName] = $item;
		}
		return $authItems;
	}

	/**
	 * @param string $name
	 * @return CAuthItem|null
	 */
	public function loadAuthItem($name)
	{
		$authItems = $this->loadAuthItems();
		return isset($authItems[$name]) ? $authItems[$name] : null;
	}

	/**
	 * @param integer $type
	 * @param integer $userId
	 * @param boolean $allowCaching
	 * @return CAuthItem[]
	 */
	public function loadAuthItems($type = null, $userId = null, $allowCaching = true)
	{
		$key = self::CACHE_KEY_PREFIX . 'authItems';

		if ($type !== null)
			$key .= '.type.' . $type;

		if ($userId !== null)
			$key .= '.userId.' . $userId;

		/* @var $cache CCache */
		if ($allowCaching && $this->cachingDuration > 0 && $this->cacheID !== false
				&& ($cache = Yii::app()->getComponent($this->cacheID)) !== null)
		{
			if (($data = $cache->get($key)) !== false)
				return unserialize($data);
		}

		$authItems = $this->owner->getAuthItems($type, $userId);

		if (isset($cache))
			$cache->set($key, serialize($authItems), $this->cachingDuration);

		return $authItems;
	}

	/**
	 * @param integer $userId
	 * @param boolean $allowCaching
	 * @return CAuthAssignment[]
	 */
	public function loadAuthAssignments($userId, $allowCaching = true)
	{
		$key = self::CACHE_KEY_PREFIX . 'authAssignments.userId.' . $userId;

		/* @var $cache CCache */
		if ($allowCaching && $this->cachingDuration > 0 && $this->cacheID !== false
				&& ($cache = Yii::app()->getComponent($this->cacheID)) !== null)
		{
			if (($data = $cache->get($key)) !== false)
				return unserialize($data);
		}

		$assignments = $this->owner->getAuthAssignments($userId);

		if (isset($cache))
			$cache->set($key, serialize($assignments), $this->cachingDuration);

		return $assignments;
	}
}
