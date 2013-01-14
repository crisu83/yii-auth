<?php

class MultilingualUrlManager extends CUrlManager
{
	/**
	 * Constructs a URL.
	 * @param string $route the controller and the action (e.g. article/read)
	 * @param array $params list of GET parameters (name=>value).
	 * @param string $ampersand the token separating name-value pairs in the URL. Defaults to '&'.
	 * @return string the constructed URL
	 * @see CUrlManager::createUrl
	 */
	public function createUrl($route, $params = array(), $ampersand = '&')
	{
		if (!isset($params['lang']))
			$params['lang'] = Yii::app()->language;

		return parent::createUrl($route, $params, $ampersand);
	}

}
