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
	 * @var string[] a list of names for the users that should be treated as administrators.
	 */
	public $admins = array('admin');

	/**
	 * Returns whether the given item has a specific parent.
	 * @param string $itemName name of the item.
	 * @param string $parentName name of the parent.
	 * @return boolean the result.
	 */
	public function hasParent($itemName, $parentName)
	{
		$permissions = $this->getItemPermissions($parentName);
		return isset($permissions[$itemName]);
	}

	/**
	 * Returns whether the given item has a specific child.
	 * @param string $itemName name of the item.
	 * @param string $childName name of the child.
	 * @return boolean the result.
	 */
	public function hasChild($itemName, $childName)
	{
		$permissions = $this->getItemPermissions($itemName);
		return isset($permissions[$childName]);
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
			if ($this->hasDescendant($childName, $itemName))
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
	 * Returns the permission tree for the given items.
	 * @param CAuthItem[] $items items to process. If omitted the complete tree will be returned.
	 * @param integer $depth current depth.
	 * @return array the permissions.
	 */
	private function getPermissions($items = null, $depth = 0)
	{
		$permissions = array();

		if ($items === null)
			$items = $this->owner->getAuthItems();

		foreach ($items as $itemName => $item)
		{
			$permissions[$itemName] = array(
				'name' => $itemName,
				'item' => $item,
				'children' => $this->getPermissions($item->getChildren(), $depth + 1),
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
}
