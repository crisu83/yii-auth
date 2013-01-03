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
	 * @param integer $userId the user id.
	 */
	public function flushAccess($itemName, $userId);
}
