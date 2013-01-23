<?php
/**
 * CachedDbAuthManager class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.components
 */

Yii::import('auth.components.ICachedAuthManager');

/**
 * Caching layer for CDbAuthManager that allows for caching access checks.
 */
class CachedDbAuthManager extends CDbAuthManager implements ICachedAuthManager
{
	const CACHE_KEY_PREFIX = 'Auth.CachedDbAuthManager.';

	/**
	 * @var integer the time in seconds that the messages can remain valid in cache.
	 * Defaults to 0, meaning the caching is disabled.
	 */
	public $cachingDuration = 0;
	/**
	 * @var string the ID of the cache application component that is used to cache the messages.
	 * Defaults to 'cache' which refers to the primary cache application component.
	 * Set this property to false if you want to disable caching.
	 */
	public $cacheID = 'cache';

	/**
	 * Performs access check for the specified user.
	 * @param string $itemName the name of the operation that need access check.
	 * @param integer $userId the user id.
	 * @param array $params name-value pairs that would be passed to biz rules associated
	 * with the tasks and roles assigned to the user.
	 * @param boolean $allowCaching whether to allow caching the result of access check.
	 * @return boolean whether the operations can be performed by the user.
	 */
	public function checkAccess($itemName, $userId, $params = array(), $allowCaching = true)
	{
		$cacheKey = $this->resolveCacheKey($itemName, $userId);
		$key = serialize($params);

		if ($allowCaching && ($cache = $this->getCache()) !== null)
		{
			if (($data = $cache->get($cacheKey)) !== false)
			{
				$data = unserialize($data);
				if (isset($data[$key]))
					return $data[$key];
			}
		}
		else
			$data = array();

		$result = $data[$key] = parent::checkAccess($itemName, $userId, $params);

		if (isset($cache))
			$cache->set($cacheKey, serialize($data), $this->cachingDuration);

		return $result;
	}

	/**
	 * Flushes the access cache for the specified user.
	 * @param string $itemName the name of the operation that need access check.
	 * @param integer $userId the user id.
	 * @return boolean whether access was flushed.
	 */
	public function flushAccess($itemName, $userId)
	{
		if (($cache = $this->getCache()) !== null)
		{
			$cacheKey = $this->resolveCacheKey($itemName, $userId);
			return $cache->delete($cacheKey);
		}
		return false;
	}

	/**
	 * Returns the key to use when caching.
	 * @param string $itemName the name of the operation that need access check.
	 * @param array $params name-value pairs that would be passed to biz rules associated
	 * with the tasks and roles assigned to the user.
	 * @return string the key.
	 */
	protected function resolveCacheKey($itemName, $userId)
	{
		return self::CACHE_KEY_PREFIX . 'checkAccess.' . $itemName . '.' . $userId;
	}

	/**
	 * Returns the caching component for this component.
	 * @return CCache|null the caching component.
	 */
	protected function getCache()
	{
		return $this->cachingDuration > 0 && $this->cacheID !== false ? Yii::app()->getComponent($this->cacheID) : null;
	}
}
