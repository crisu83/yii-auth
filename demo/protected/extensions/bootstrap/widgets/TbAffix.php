<?php
/**
 * TbAffix class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package bootstrap.widgets
 * @since 2.0.0
 */

/**
 * Bootstrap affix widget.
 * @see http://twitter.github.com/bootstrap/javascript.html#affix
 */
class TbAffix extends CWidget
{
	const CONTAINER_PREFIX = 'yii_bootstrap_affix_';

	/**
	 * @var string the name of the affix element. Defaults to 'div'.
	 */
	public $tagName = 'div';
	/**
	 * @var array the options for the Bootstrap Javascript plugin.
	 */
	public $options = array();
	/**
	 * @var array the HTML attributes for the widget container.
	 */
	public $htmlOptions = array();

	private static $_containerId = 0;

	/**
	 * Initializes the widget.
	 */
	public function init()
	{
		echo CHtml::openTag($this->tagName, $this->htmlOptions);
	}

	/**
	 * Runs the widget.
	 */
	public function run()
	{
		$id = $this->htmlOptions['id'];

		echo CHtml::closeTag($this->tagName);

		/** @var CClientScript $cs */
		$cs = Yii::app()->getClientScript();
		$options = !empty($this->options) ? CJavaScript::encode($this->options) : '';
		$cs->registerScript(__CLASS__.'#'.$id, "jQuery('#{$id}').affix({$options});");
	}

	/**
	 * Returns the next affix container ID.
	 * @return string the id
	 * @static
	 */
	public static function getNextContainerId()
	{
		return self::CONTAINER_PREFIX.self::$_containerId++;
	}
}
