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
 * Bootstrap form builder input element.
 */
class TbFormInputElement extends CFormInputElement {
	/**
	 * @var array the data for list inputs.
	 */
	public $data;
	/**
	 * @var array additional HTML options to be rendered in the input tag.
	 */
	public $htmlOptions = array();

	/**
	 * Renders everything for this input.
	 * @return string the complete rendering result for this input, including label, input field, hint, and error.
	 */
    public function render()
    {
        $form = $this->getParent();
        return $form->getActiveFormWidget()->inputRow($this->type, $form->getModel(), $this->name, $this->data, $this->htmlOptions);
    }
}
