<?php
/**
 * TbFormButtonElement class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package bootstrap.form
 * @since 2.0.0
 */

/**
 * Bootstrap form builder button element.
 */
class TbFormButtonElement extends CFormButtonElement {
	/**
	 * @var string the button callback types.
	 * @see TbButton::$buttonType
	 */
	public $buttonType;
	/**
	 * @var string the button type.
	 * @see TbButton::$type
	 */
	public $type;
	/**
	 * @var string the button size.
	 * @see TbButton::$size
	 */
	public $size;
	/**
	 * @var string the button icon.
	 * @see TbButton::$icon
	 */
	public $icon;
	/**
	 * @var string the button URL.
	 * @see TbButton::$url
	 */
	public $url;
	/**
	 * @var boolean indicates whether the button should span the full width of the a parent.
	 */
	public $block = false;
	/**
	 * @var boolean indicates whether the button is active.
	 */
	public $active = false;
	/**
	 * @var boolean indicates whether the button is disabled.
	 */
	public $disabled = false;
	/**
	 * @var boolean indicates whether to encode the label.
	 */
	public $encodeLabel = true;
	/**
	 * @var boolean indicates whether to enable toggle.
	 */
	public $toggle;
	/**
	 * @var string the text to display while loading.
	 */
	public $loadingText;
	/**
	 * @var string the text to display when loading is complete.
	 */
	public $completeText;
	/**
	 * @var array the dropdown button items.
	 */
	public $items = array();
	/**
	 * @var array the HTML attributes for the widget container.
	 */
	public $htmlOptions = array();
	/**
	 * @var array array the button ajax options.
	 * @see TbButton::$ajaxOptions
	 */
	public $ajaxOptions = array();
	/**
	 * @var array the HTML attributes for the dropdown menu.
	 */
	public $dropdownOptions = array();

	/**
	 * Returns this button.
	 * @return string the rendering result
	 */
    public function render()
    {
        ob_start();
        $this->getParent()->getOwner()->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>isset($this->buttonType) ? $this->buttonType : null,
            'type'=>isset($this->type) ? $this->type : null,
            'size'=>isset($this->size) ? $this->size : null,
            'icon'=>$this->icon,
            'label'=>$this->label,
            'url'=>$this->url,
            'block'=>$this->block,
            'active'=>$this->active,
            'disabled'=>$this->disabled,
            'encodeLabel'=>$this->encodeLabel,
            'toggle'=>$this->toggle,
            'loadingText'=>$this->loadingText,
            'completeText'=>$this->completeText,
            'items'=>$this->items,
            'htmlOptions'=>$this->htmlOptions,
            'ajaxOptions'=>$this->ajaxOptions,
            'dropdownOptions'=>$this->dropdownOptions,
        ));
        return ob_get_clean();
    }
}
