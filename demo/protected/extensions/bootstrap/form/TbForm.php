<?php
/**
 * TbForm class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2012-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package bootstrap.form
 * @since 2.0.0
 */

Yii::import('bootstrap.form.*');

/**
 * Bootstrap form builder.
 */
class TbForm extends CForm {
	/**
	 * @var string the name of the class for representing a form input element.
	 */
    public $buttonElementClass = 'TbFormButtonElement';
	/**
	 * @var string the name of the class for representing a form button element.
	 */
    public $inputElementClass = 'TbFormInputElement';
	/**
	 * @var array the configuration used to create the active form widget.
	 */
    public $activeForm = array('class'=>'bootstrap.widgets.TbActiveForm');

	/**
	 * Renders a single element which could be an input element, a sub-form, a string, or a button.
	 * @param mixed $element the form element to be rendered
	 * @return string the rendering result
	 */
    public function renderElement($element)
    {
        if(is_string($element))
        {
            if(($e=$this[$element])===null && ($e=$this->getButtons()->itemAt($element))===null)
                return $element;
            else
                $element=$e;
        }
        if($element->getVisible())
        {
            if($element instanceof CFormInputElement)
            {
                if($element->type==='hidden')
                    return '<div class="hidden">'.$element->render().'</div>';
            }

            return $element->render();
        }
        return '';
    }

	/**
	 * Renders the {@link buttons} in this form.
	 * @return string the rendering result
	 */
    public function renderButtons()
    {
        $output='';
        foreach($this->getButtons() as $button)
            $output.=$this->renderElement($button);
        return $output!=='' ? '<div class="form-actions">'.$output.'</div>' : '';
    }
}
