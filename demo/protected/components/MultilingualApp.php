<?php

class MultilingualApp extends CBehavior
{
	public $defaultLocale = 'en_us';

	public $languages = array('en_us');

	/**
	 * @return array the behavior events.
	 */
	public function events()
	{
		return array(
			'onBeginRequest'=>'setLanguage',
		);
	}

	/**
	 * Sets the application language from a user state if applicable.
	 */
	protected function setLanguage()
	{
		$matches = array();
		if ($this->owner->user->hasState('__locale'))
			$language = $this->owner->user->getState('__locale');
		else if (preg_match('/^\/([a-z]{2}(?:_[a-z]{2})?)\//i',
			substr($this->owner->request->url, strlen($this->owner->baseUrl)), $matches) !== false
			&& isset($matches[1], $this->languages[$matches[1]]))
			$language = $matches[1];
		else
			$language = $this->defaultLocale;

		$this->owner->language = $language;
	}
}
