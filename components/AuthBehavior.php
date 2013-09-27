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
 * @property CAuthManager|IAuthManager $owner The authorization manager.
 */
class AuthBehavior extends CBehavior
{
    /**
     * @var array cached relations between the auth items.
     */
    private $_items = array();

    /**
     * Return thee parents and children of specific item or all items
     * @param string $itemName name of the item.
     * @return array
     */
    public function getItems($itemName = null)
    {
        if ($itemName && isset($this->_items[$itemName])) {
            return $this->_items[$itemName];
        }

        return $this->_items;
    }

    /**
     * Sets the parents of specific item
     * @param string $itemName name of the item.
     * @param array $parents
     */
    public function setItemParents($itemName, $parents)
    {
        $this->_items[$itemName]['parents'] = $parents;
    }

    /**
     * Sets the children of specific item
     * @param string $itemName name of the item.
     * @param array $children
     */
    public function setItemChildren($itemName, $children)
    {
        $this->_items[$itemName]['children'] = $children;
    }

    /**
     * Gets the parents of specific item if exists
     * @param string $itemName name of the item.
     * @return array
     */
    public function getParents($itemName)
    {
        $items = $this->getItems($itemName);
        if (isset($items['parents'])) {
            return $items['parents'];
        }

        return array();
    }

    /**
     * Gets the children of specific item if exists
     * @param string $itemName name of the item.
     * @return array
     */
    public function getChildren($itemName)
    {
        $items = $this->getItems($itemName);
        if (isset($items['children'])) {
            return $items['children'];
        }

        return array();
    }

    /**
     * Returns whether the given item has a specific parent.
     * @param string $itemName name of the item.
     * @param string $parentName name of the parent.
     * @return boolean the result.
     */
    public function hasParent($itemName, $parentName)
    {
        $parents = $this->getParents($itemName);
        if (in_array($parentName, $parents)) {
            return true;
        }
        return false;
    }

    /**
     * Returns whether the given item has a specific child.
     * @param string $itemName name of the item.
     * @param string $childName name of the child.
     * @return boolean the result.
     */
    public function hasChild($itemName, $childName)
    {
        $children = $this->getChildren($itemName);
        if (in_array($childName, $children)) {
            return true;
        }
        return false;
    }

    /**
     * Returns whether the given item has a specific ancestor.
     * @param string $itemName name of the item.
     * @param string $ancestorName name of the ancestor.
     * @return boolean the result.
     */
    public function hasAncestor($itemName, $ancestorName)
    {
        $ancestors = $this->getAncestors($itemName);
        return isset($ancestors[$ancestorName]);
    }

    /**
     * Returns whether the given item has a specific descendant.
     * @param string $itemName name of the item.
     * @param string $descendantName name of the descendant.
     * @return boolean the result.
     */
    public function hasDescendant($itemName, $descendantName)
    {
        $descendants = $this->getDescendants($itemName);
        return isset($descendants[$descendantName]);
    }

    /**
     * Returns flat array of all ancestors.
     * @param string $itemName name of the item.
     * @return array the ancestors.
     */
    public function getAncestors($itemName)
    {
        $ancestors = $this->getAncestor($itemName);
        return $this->flattenPermissions($ancestors);
    }

    /**
     * Returns all ancestors for the given item recursively.
     * @param string $itemName name of the item.
     * @param integer $depth current depth.
     * @return array the ancestors.
     */
    public function getAncestor($itemName, $depth = 0)
    {
        $ancestors = array();
        $parents = $this->getParents($itemName);
        if (empty($parents)) {
            $parents = $this->owner->db->createCommand()
                ->select('parent')
                ->from($this->owner->itemChildTable)
                ->where('child=:child', array(':child' => $itemName))
                ->queryColumn();
            $this->setItemParents($itemName, $parents);
        }

        foreach ($parents as $parent) {
            $ancestors[] = array(
                'name' => $parent,
                'item' => $this->owner->getAuthItem($parent),
                'parents' => $this->getAncestor($parent, $depth + 1),
                'depth' => $depth
            );
        }
        return $ancestors;
    }

    /**
     * Returns flat array of all the descendants.
     * @param string $itemName name of the item.
     * @return array the descendants.
     */
    public function getDescendants($itemName)
    {
        $descendants = $this->getDescendant($itemName);
        return $this->flattenPermissions($descendants);
    }

    /**
     * Returns all the descendants for the given item recursively.
     * @param string $itemName name of the item.
     * @param integer $depth current depth.
     * @return array the descendants.
     */
    public function getDescendant($itemName, $depth = 0)
    {
        $descendants = array();
        $children = $this->getChildren($itemName);
        if (empty($children)) {
            $children = $this->owner->db->createCommand()
                ->select('child')
                ->from($this->owner->itemChildTable)
                ->where('parent=:parent', array(':parent' => $itemName))
                ->queryColumn();
            $this->setItemChildren($itemName, $children);
        }

        foreach ($children as $child) {
            $descendants[$child] = array(
                'name' => $child,
                'item' => $this->owner->getAuthItem($child),
                'children' => $this->getDescendant($child, $depth + 1),
                'depth' => $depth,
            );
        }
        return $descendants;
    }

    /**
     * Returns the permission tree for the given items.
     * @param CAuthItem[] $items items to process. If omitted the complete tree will be returned.
     * @param integer $depth current depth.
     * @return array the permissions.
     */
    private function getPermissions($items = null, $depth = 0)
    {
        $permissions = array();

        if ($items === null) {
            $items = $this->owner->getAuthItems();
        }

        foreach ($items as $itemName => $item) {
            $permissions[$itemName] = array(
                'name' => $itemName,
                'item' => $item,
                'children' => $this->getPermissions($item, $depth + 1),
                'depth' => $depth,
            );
        }

        return $permissions;
    }

    /**
     * Builds the permissions for the given item.
     * @param string $itemName name of the item.
     * @return array the permissions.
     */
    private function getItemPermissions($itemName)
    {
        $item = $this->owner->getAuthItem($itemName);
        return $item instanceof CAuthItem ? $this->getPermissions($item->getChildren()) : array();
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

        foreach ($flat as $itemName => $item) {
            if (in_array($itemName, $names)) {
                $permissions[$itemName] = $item;
            }
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
        foreach ($permissions as $itemName => $itemPermissions) {
            $flattened[$itemName] = $itemPermissions;

            if (isset($itemPermissions['children'])) {
                $children = $itemPermissions['children'];
                unset($itemPermissions['children']); // not needed in a flat tree
                $flattened = array_merge($flattened, $this->flattenPermissions($children));
            }

            if (isset($itemPermissions['parents'])) {
                $parents = $itemPermissions['parents'];
                unset($itemPermissions['parents']);
                $flattened = array_merge($flattened, $this->flattenPermissions($parents));
            }
        }
        return $flattened;
    }
}
