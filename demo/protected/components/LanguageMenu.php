<?php

Yii::import('bootstrap.widgets.TbNav');

class LanguageMenu extends TbNav
{
	/**
	 * Initializes the widget.
	 */
	public function init()
	{
		$languages = Yii::app()->getLanguages();
		$activeLocale = Yii::app()->language;

		$items = array(array('label'=>'Language'));

		foreach ($languages as $locale => $language)
		{
			if ($locale === $activeLocale)
				$activeLanguage = $language;

			$items[] = array(
				'label' => $language,
				'url' => array('/site/changeLanguage', 'locale'=>$locale),
				'active' => $locale === $activeLocale,
			);
		}

		$label = isset($activeLanguage) ? $activeLanguage : 'Unknown';
		$this->items = array_merge(array(array('label'=> $label, 'items'=>$items)), $this->items);

		parent::init();
	}
}
