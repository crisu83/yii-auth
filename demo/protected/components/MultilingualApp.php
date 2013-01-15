<?php

class MultilingualApp extends CBehavior
{
	/**
	 * @var string the default locale.
	 */
	public $defaultLanguage;
	/**
	 * @var array a list of languages enabled for the application.
	 */
	public $languages = array('en');

	/**
	 * @return array the behavior events.
	 */
	public function events()
	{
		return array(
			'onBeginRequest'=>'setLanguage',
		);
	}

	public function init()
	{
		if (!isset($this->defaultLanguage))
			$this->defaultLanguage = $this->owner->sourceLanguage;
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
			$language = $this->defaultLanguage;

		$this->owner->language = $language;
	}

	/**
	 * A list of the languages enabled for the application.
	 * @return array list of languages.
	 */
	public function getLanguages()
	{
		return $this->languages;
	}
}
