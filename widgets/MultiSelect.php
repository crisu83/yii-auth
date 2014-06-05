<?php
/**
 * MultiSelect widget file.
 * @author Arnaud Fabre <https://github.com/arnaud-f>
 * @copyright Copyright &copy; Arnaud Fabre 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.widgets
 */

class MultiSelect extends CWidget
{
    /**
     * Nom du modèle des objets de la liste
     */
    public $propertyName;

    /**
     * Nom du modèle parent
     */
    public $modelName;

    /**
     * Tableau des éléments à afficher
     * $elements = array($elements, valueField, nameField)
     */
    public $elements;

    /**
     * Label du champs du formulaire
     */
    public $label;

    public function run()
    {
        echo '
        <label for="multiSelect' . $this->propertyName .'">' . $this->label . '</label><br />
            <select
            name="' . $this->modelName . '[' . $this->propertyName . '][]"
            class="selectpicker"
            data-live-search="true"
            id="multiSelect' . $this->propertyName . '"
            multiple>
        ';

        function cmp($a, $b) {
            if(is_array($a) && is_array($b))
                return count($a) - count($b);
            else
                return 0;
        }
        uasort($this->elements, 'cmp');

        foreach ($this->elements as $groupId => $group)
        {
            if(!is_array($group)) continue;

            echo '<optgroup label="' . $groupId . '">';
            foreach ($group as $key => $element) {
                    echo '<option value="' . $key .'" data-subtext="'. $element .'">' . $key . '</option>';
            }
            echo '</optgroup>';
        }
        echo '</select>';

        $this->registerClientScript();
    }

    public function registerClientScript()
    {
        $cs = Yii::app()->getClientScript();

        $assetsUrl = realpath(__DIR__ . '/../assets');
        $css = Yii::app()->getAssetManager()->publish($assetsUrl . '/css/bootstrap-select.min.css');
        $js = Yii::app()->getAssetManager()->publish($assetsUrl . '/js/bootstrap-select.min.js');
        $cs->registerCssFile($css);
        $cs->registerScriptFile($js);

        $cs->registerScript('authJs', "
            $(document).ready(function(){
                 $('.selectpicker').selectpicker({
                    'selectedTextFormat': 'count'
                });
            })
        ");
    }

}
