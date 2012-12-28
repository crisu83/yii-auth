<?php

/**
 * @property CAuthManager $owner
 */
class AuthBehavior extends CBehavior
{
    const CACHE_KEY_PREFIX = 'AuthModule.AuthBehavior.';

    public $cachingDuration = 0;
    public $cacheID = 'cache';

    /**
     * @param string $itemName
     * @param string $childName
     * @return boolean
     */
    public function hasPermission($itemName, $childName)
    {
        $descendants = $this->getDescendants($itemName);
        return isset($descendants[$childName]);
    }

    /**
     * @param string $itemName
     * @param string $parentName
     * @return boolean
     */
    public function hasParent($itemName, $parentName)
    {
        $parentPermissions = $this->getItemPermissions($parentName);
        return isset($parentPermissions[$itemName]);
    }

    /**
     * @param string $itemName
     * @param string $childName
     * @return boolean
     */
    public function hasChild($itemName, $childName)
    {
        $itemPermissions = $this->getItemPermissions($itemName);
        return isset($itemPermissions[$childName]);
    }

    /**
     * @param string $itemName
     * @param array|null $permissions
     * @return array
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
     * @param string $itemName
     * @return array
     */
    public function getDescendants($itemName)
    {
        $itemPermissions = $this->getItemPermissions($itemName);

        return $this->flattenPermissions($itemPermissions);
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
     * @param array|null $items
     * @param integer $depth
     * @param array $processed
     * @return array
     */
    private function buildPermissions($items = null, $depth = 0, &$processed = array())
    {
        $permissions = array();

        if ($items === null)
            $items = $this->loadAuthItems();

        foreach ($items as $itemName => $item)
        {
            if (!isset($processed[$itemName]))
            {
                $permissions[$itemName] = array(
                    'name' => $itemName,
                    'item' => $item,
                    'children' => $this->buildPermissions($item->getChildren(), $depth + 1, $processed),
                    'depth' => $depth,
                );
                $processed[$itemName] = $itemName;
            }
        }

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
     * @param string $itemName
     * @param integer $depth
     * @return array
     */
    private function buildItemPermissions($itemName, $depth = 0)
    {
        $permissions = array();
        $item = $this->loadAuthItem($itemName);
        if ($item instanceof CAuthItem)
        {
            foreach ($item->getChildren() as $childName => $childItem)
            {
                $permissions[$childName] = array(
                    'name' => $childName,
                    'item' => $childItem,
                    'children' => $this->buildItemPermissions($childName, $depth + 1),
                    'depth' => $depth,
                );
            }
        }

        return $permissions;
    }

    /**
     * @param string[] $names
     * @return array()
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
     * @param array $permissions
     * @return array
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
     * @param string $name
     * @param boolean $allowCaching
     * @return CAuthItem|null
     */
    public function loadAuthItem($name, $allowCaching = true)
    {
        $authItems = $this->loadAuthItems(null, null, $allowCaching);
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
     * @param integer $userId
     * @param boolean $allowCaching
     * @return CAuthAssignment[]
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
