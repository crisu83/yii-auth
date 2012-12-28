<?php
/**
 * Bootstrap class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2011-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version 1.1.0
 */

/**
 * Bootstrap application component.
 */
class Bootstrap extends CApplicationComponent
{
	// Bootstrap plugins.
	const PLUGIN_AFFIX = 'affix';
	const PLUGIN_ALERT = 'alert';
    const PLUGIN_BUTTON = 'button';
    const PLUGIN_CAROUSEL = 'carousel';
    const PLUGIN_COLLAPSE = 'collapse';
    const PLUGIN_DROPDOWN = 'dropdown';
    const PLUGIN_MODAL = 'modal';
    const PLUGIN_POPOVER = 'popover';
    const PLUGIN_SCROLLSPY = 'scrollspy';
    const PLUGIN_TAB = 'tab';
    const PLUGIN_TOOLTIP = 'tooltip';
    const PLUGIN_TRANSITION = 'transition';
    const PLUGIN_TYPEAHEAD = 'typeahead';

	/**
	 * @var array plugin initial options (name=>options).
	 * Each array key-value pair represents the initial options for a single plugin class,
	 * with the array key being the plugin name, and array value being the initial options array.
	 * @since 0.9.8
	 */
	public $plugins = array();
	/**
	 * @var boolean indicates whether assets should be republished on every request.
	 */
	public $forceCopyAssets = false;

	protected $_assetsUrl;

	/**
	 * Registers the Bootstrap CSS.
	 */
	public function registerCoreCss()
	{
		$filename = YII_DEBUG ? 'bootstrap.css' : 'bootstrap.min.css';
		Yii::app()->clientScript->registerCssFile($this->getAssetsUrl().'/css/'.$filename);
	}

	/**
	 * Registers the Bootstrap responsive CSS.
	 * @since 0.9.8
	 */
	public function registerResponsiveCss()
	{
		/** @var CClientScript $cs */
		$cs = Yii::app()->getClientScript();
		$cs->registerMetaTag('width=device-width, initial-scale=1.0', 'viewport');
		$filename = YII_DEBUG ? 'bootstrap-responsive.css' : 'bootstrap-responsive.min.css';
		$cs->registerCssFile($this->getAssetsUrl().'/css/'.$filename);
	}

	/**
	 * Registers the Yii-specific CSS missing from Bootstrap.
	 * @since 0.9.11
	 */
	public function registerYiiCss()
	{
		Yii::app()->clientScript->registerCssFile($this->getAssetsUrl().'/css/yii.css');
	}

	/**
	 * Registers all Bootstrap CSS.
	 * @since 2.0.0
	 */
	public function registerAllCss()
	{
		$this->registerCoreCss();
		$this->registerResponsiveCss();
		$this->registerYiiCss();
	}

	/**
	 * Registers the core JavaScript.
	 * @since 0.9.8
	 */
	public function registerCoreScripts()
	{
		$this->registerJS(Yii::app()->clientScript->coreScriptPosition);
		$this->registerPopover(); // popover also registers tooltip
	}

	/**
	 * Registers the Bootstrap JavaScript.
	 * @param int $position the position of the JavaScript code.
	 */
	protected function registerJS($position = CClientScript::POS_HEAD)
	{
		/** @var CClientScript $cs */
		$cs = Yii::app()->getClientScript();
		$cs->registerCoreScript('jquery');
		$filename = YII_DEBUG ? 'bootstrap.js' : 'bootstrap.min.js';
		$cs->registerScriptFile($this->getAssetsUrl().'/js/'.$filename, $position);
	}

	/**
	 * Registers all Bootstrap CSS and JavaScript.
	 * @since 2.1.0
	 */
	public function register()
	{
		$this->registerAllCss();
		$this->registerCoreScripts();
	}

	/**
	 * Registers the Bootstrap affix plugin.
	 * @param string $selector the CSS selector
	 * @param array $options the plugin options
	 * @see http://twitter.github.com/bootstrap/javascript.html#affix
	 * @since 2.0.0
	 */
	public function registerAffix($selector = null, $options = array())
	{
		$this->registerPlugin(self::PLUGIN_AFFIX, $selector, $options);
	}

	/**
	 * Registers the Bootstrap alert plugin.
	 * @param string $selector the CSS selector
	 * @param array $options the plugin options
	 * @see http://twitter.github.com/bootstrap/javascript.html#alerts
	 * @since 0.9.8
	 */
	public function registerAlert($selector = null, $options = array())
	{
		$this->registerPlugin(self::PLUGIN_ALERT, $selector, $options);
	}

	/**
	 * Registers the Bootstrap buttons plugin.
	 * @param string $selector the CSS selector
	 * @param array $options the plugin options
	 * @see http://twitter.github.com/bootstrap/javascript.html#buttons
	 * @since 0.9.8
	 */
	public function registerButton($selector = null, $options = array())
	{
		$this->registerPlugin(self::PLUGIN_BUTTON, $selector, $options);
	}

	/**
	 * Registers the Bootstrap carousel plugin.
	 * @param string $selector the CSS selector
	 * @param array $options the plugin options
	 * @see http://twitter.github.com/bootstrap/javascript.html#carousel
	 * @since 0.9.8
	 */
	public function registerCarousel($selector = null, $options = array())
	{
		$this->registerPlugin(self::PLUGIN_CAROUSEL, $selector, $options);
	}

	/**
	 * Registers the Bootstrap collapse plugin.
	 * @param string $selector the CSS selector
	 * @param array $options the plugin options
	 * @see http://twitter.github.com/bootstrap/javascript.html#collapse
	 * @since 0.9.8
	 */
	public function registerCollapse($selector = null, $options = array())
	{
		$this->registerPlugin(self::PLUGIN_COLLAPSE, $selector, $options);
	}

	/**
	 * Registers the Bootstrap dropdowns plugin.
	 * @param string $selector the CSS selector
	 * @param array $options the plugin options
	 * @see http://twitter.github.com/bootstrap/javascript.html#dropdowns
	 * @since 0.9.8
	 */
	public function registerDropdown($selector = null, $options = array())
	{
		$this->registerPlugin(self::PLUGIN_DROPDOWN, $selector, $options);
	}

	/**
	 * Registers the Bootstrap modal plugin.
	 * @param string $selector the CSS selector
	 * @param array $options the plugin options
	 * @see http://twitter.github.com/bootstrap/javascript.html#modal
	 * @since 0.9.8
	 */
	public function registerModal($selector = null, $options = array())
	{
		$this->registerPlugin(self::PLUGIN_MODAL, $selector, $options);
	}

	/**
	 * Registers the Bootstrap scrollspy plugin.
	 * @param string $selector the CSS selector
	 * @param array $options the plugin options
	 * @see http://twitter.github.com/bootstrap/javascript.html#scrollspy
	 * @since 0.9.8
	 */
	public function registerScrollSpy($selector = null, $options = array())
	{
		$this->registerPlugin(self::PLUGIN_SCROLLSPY, $selector, $options);
	}

	/**
	 * Registers the Bootstrap popover plugin.
	 * @param string $selector the CSS selector
	 * @param array $options the plugin options
	 * @see http://twitter.github.com/bootstrap/javascript.html#popover
	 * @since 0.9.8
	 */
	public function registerPopover($selector = null, $options = array())
	{
		$this->registerTooltip(); // Popover requires the tooltip plugin
		if (!isset($options['selector']))
			$options['selector'] = $selector !== null ? $selector : 'a[rel=popover]';
		$this->registerPlugin(self::PLUGIN_POPOVER, 'body', $options);
	}

	/**
	 * Registers the Bootstrap tabs plugin.
	 * @param string $selector the CSS selector
	 * @param array $options the plugin options
	 * @see http://twitter.github.com/bootstrap/javascript.html#tabs
	 * @since 0.9.8
	 */
	public function registerTabs($selector = null, $options = array())
	{
		$this->registerPlugin(self::PLUGIN_TAB, $selector, $options);
	}

	/**
	 * Registers the Bootstrap tooltip plugin.
	 * @param string $selector the CSS selector
	 * @param array $options the plugin options
	 * @see http://twitter.github.com/bootstrap/javascript.html#tooltip
	 * @since 0.9.8
	 */
	public function registerTooltip($selector = null, $options = array())
	{
		if (!isset($options['selector']))
			$options['selector'] = $selector !== null ? $selector : 'a[rel=tooltip]';
		$this->registerPlugin(self::PLUGIN_TOOLTIP, 'body', $options);
	}

	/**
	 * Registers the Bootstrap typeahead plugin.
	 * @param string $selector the CSS selector
	 * @param array $options the plugin options
	 * @see http://twitter.github.com/bootstrap/javascript.html#typeahead
	 * @since 0.9.8
	 */
	public function registerTypeahead($selector = null, $options = array())
	{
		$this->registerPlugin(self::PLUGIN_TYPEAHEAD, $selector, $options);
	}

	/**
	 * Registers a Bootstrap JavaScript plugin.
	 * @param string $name the name of the plugin
	 * @param string $selector the CSS selector
	 * @param array $options the plugin options
	 * @param string $defaultSelector the default CSS selector
	 * @since 0.9.8
	 */
	protected function registerPlugin($name, $selector = null, $options = array())
	{
		// Initialization from extension configuration.
		$config = isset($this->plugins[$name]) ? $this->plugins[$name] : array();

		if ($selector === null && isset($config['selector']))
			$selector = $config['selector'];

		if (isset($config['options']))
			$options = !empty($options) ? CMap::mergeArray($options, $config['options']) : $config['options'];

		if ($selector !== null)
		{
			$key = __CLASS__.'.'.md5($name.$selector.serialize($options));
			$options = !empty($options) ? CJavaScript::encode($options) : '';
			Yii::app()->clientScript->registerScript($key, "jQuery('{$selector}').{$name}({$options});");
		}
	}

	/**
	* Returns the URL to the published assets folder.
	* @return string the URL
	*/
	protected function getAssetsUrl()
	{
		if (isset($this->_assetsUrl))
			return $this->_assetsUrl;
		else
		{
			$assetsPath = Yii::getPathOfAlias('bootstrap.assets');
			$assetsUrl = Yii::app()->assetManager->publish($assetsPath, true, -1, $this->forceCopyAssets);
			return $this->_assetsUrl = $assetsUrl;
		}
	}

    /**
     * Returns the extension version number.
     * @return string the version
     */
    public function getVersion()
    {
        return '2.1.0';
    }
}
