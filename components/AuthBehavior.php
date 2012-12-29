<?php
/**
 * AuthBehavior class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.components
 */

/**
 * Auth module behavior for the authorization manager.
 *
 * @property CAuthManager $owner
 */
class AuthBehavior extends CBehavior
{
    const CACHE_KEY_PREFIX = 'AuthModule.AuthBehavior.';

	/**
	 * @var integer the time in seconds that the messages can remain valid in cache.
	 * Defaults to 0, meaning the caching is disabled.
	 */
	public $cachingDuration = 0;
	/**
	 * @var string the ID of the cache application component that is used to cache the messages.
	 * Defaults to 'cache' which refers to the primary cache application component.
	 * Set this property to false if you want to disable caching the permissions.
	 */
	public $cacheID = 'cache';

    /**
	 * Returns whether the given item has a specific permission.
     * @param string $itemName name of the item.
     * @param string $childName name of the permission.
     * @return boolean the result.
     */
    public function hasPermission($itemName, $childName)
    {
        $descendants = $this->getDescendants($itemName);
        return isset($descendants[$childName]);
    }

    /**
	 * Returns whether the given item has a specific parent.
     * @param string $itemName name of the item.
     * @param string $parentName name of the parent.
     * @return boolean the result.
     */
    public function hasParent($itemName, $parentName)
    {
        $parentPermissions = $this->getItemPermissions($parentName);
        return isset($parentPermissions[$itemName]);
    }

    /**
	 * Returns whether the given item has a specific child.
     * @param string $itemName name of the item.
     * @param string $childName name of the child.
     * @return boolean the result.
     */
    public function hasChild($itemName, $childName)
    {
        $itemPermissions = $this->getItemPermissions($itemName);
        return isset($itemPermissions[$childName]);
    }

    /**
	 * Returns all ancestors for the given item recursively.
     * @param string $itemName name of the item.
     * @param array|null $permissions permissions to process.
     * @return array the ancestors.
     */
    public function getAncestors($itemName, $permissions = null)
    {
        $ancestors = array();

        if ($permissions === null)
            $permissions = $this->getPermissions();

        foreach ($permissions as $childName => $child)
        {
            if ($this->hasPermission($childName, $itemName))
                $ancestors[$childName] = $child;

            $ancestors = array_merge($ancestors, $this->getAncestors($itemName, $child['children']));
        }

        return $ancestors;
    }

    /**
	 * Returns all the descendants for the given item recursively.
     * @param string $itemName name of the item.
     * @return array the descendants.
     */
    public function getDescendants($itemName)
    {
        $itemPermissions = $this->getItemPermissions($itemName);
        return $this->flattenPermissions($itemPermissions);
    }

    /**
	 * Returns the complete permissions tree.
     * @param boolean $allowCaching whether to allow caching the result of access check.
     * @return array the permissions.
     */
    public function getPermissions($allowCaching = true)
    {
        $key = self::CACHE_KEY_PREFIX . 'permissions';

        /* @var $cache CCache */
        if ($allowCaching && $this->cachingDuration > 0 && $this->cacheID !== false
                && ($cache = Yii::app()->getComponent($this->cacheID)) !== null
        )
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
	 * Builds the permission tree to the given items.
     * @param array|null $items items to process. If omitted the complete tree will be built.
     * @param integer $depth current depth.
     * @return array the permissions.
     */
    private function buildPermissions($items = null, $depth = 0)
    {
        $permissions = array();

        if ($items === null)
            $items = $this->loadAuthItems();

        foreach ($items as $itemName => $item)
        {
			$permissions[$itemName] = array(
				'name' => $itemName,
				'item' => $item,
				'children' => $this->buildPermissions($item->getChildren(), $depth + 1),
				'depth' => $depth,
			);
        }

        return $permissions;
    }

    /**
	 * Returns the permissions for the given item.
     * @param string $itemName name of the item.
     * @param boolean $allowCaching whether to allow caching the result of access check.
     * @return array the permissions.
     */
    public function getItemPermissions($itemName, $allowCaching = true)
    {
        $key = self::CACHE_KEY_PREFIX . 'itemPermissions.' . $itemName;

        /* @var $cache CCache */
        if ($allowCaching && $this->cachingDuration > 0 && $this->cacheID !== false
                && ($cache = Yii::app()->getComponent($this->cacheID)) !== null
        )
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
	 * Builds the permissions for the given item.
     * @param string $itemName name of the item.
     * @return array the permissions.
     */
    private function buildItemPermissions($itemName)
    {
        $item = $this->loadAuthItem($itemName);
		return $this->buildPermissions($item->getChildren());
    }

    /**
	 * Returns the permissions for the items with the given names.
     * @param string[] $names list of item names.
     * @return array the permissions.
     */
    public function getItemsPermissions($names)
    {
        $permissions = array();

        $items = $this->getPermissions();
        $flat = $this->flattenPermissions($items);

        foreach ($flat as $itemName => $item)
        {
            if (in_array($itemName, $names))
                $permissions[$itemName] = $item;
        }

        return $permissions;
    }

    /**
	 * Flattens the given permission tree.
     * @param array $permissions the permissions tree.
     * @return array the permissions.
     */
    public function flattenPermissions($permissions)
    {
        $flattened = array();
        foreach ($permissions as $itemName => $itemPermissions)
        {
            $children = $itemPermissions['children'];
            unset($itemPermissions['children']); // not needed in a flat tree
            $flattened[$itemName] = $itemPermissions;
            $flattened = array_merge($flattened, $this->flattenPermissions($children));
        }

        return $flattened;
    }

    /**
	 * Returns the auth item with the given name.
     * @param string $name name of the item.
     * @param boolean $allowCaching whether to allow caching the result of access check.
     * @return CAuthItem the authorization item.
     */
    public function loadAuthItem($name, $allowCaching = true)
    {
        $authItems = $this->loadAuthItems(null, null, $allowCaching);
        return isset($authItems[$name]) ? $authItems[$name] : null;
    }

    /**
	 * Returns all the authorization items of a given type or for a given user.
     * @param integer $type type of item.
     * @param integer $userId the user id.
     * @param boolean $allowCaching whether to allow caching the result of access check.
     * @return CAuthItem[] the authorization items.
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
            && ($cache = Yii::app()->getComponent($this->cacheID)) !== null
        )
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
	 * Returns all the authorization assignments for the given user.
     * @param integer $userId the user id.
     * @param boolean $allowCaching whether to allow caching the result of access check.
     * @return CAuthAssignment[] the authorization assignments.
     */
    public function loadAuthAssignments($userId, $allowCaching = true)
    {
        $key = self::CACHE_KEY_PREFIX . 'authAssignments.userId.' . $userId;

        /* @var $cache CCache */
        if ($allowCaching && $this->cachingDuration > 0 && $this->cacheID !== false
                && ($cache = Yii::app()->getComponent($this->cacheID)) !== null
        )
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
