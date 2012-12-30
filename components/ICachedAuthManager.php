<?php
/**
 * ICachedAuthManager class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.components
 */

/**
 * Interface for cached authorization managers.
 */
interface ICachedAuthManager
{
	/**
	 * Flushes the access cache for the specified user.
	 * @param string $itemName the name of the operation that need access check.
	 * @param mixed $userId the user id.
	 * @param array $params name-value pairs that would be passed to biz rules associated
	 * with the tasks and roles assigned to the user.
	 */
	public function flushAccess($itemName, $userId, $params = array());
}
