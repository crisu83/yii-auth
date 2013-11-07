<?php
/**
 * GraphController class file.
 * @author Arnaud Fabre <https://github.com/arnaud-f>
 * @copyright Copyright &copy; Arnaud Fabre 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package auth.controllers
 */
class GraphController extends AuthController
{

    public function actionIndex()
    {
        $this->render('rbac', array(
        	'root' => 'Admin',
        ));
    }

    public function actionRBACJson($root = 'Admin')
    {
		$am = Yii::app()->getAuthManager();
        $this->renderJSON(array(
        	'nbelements' => count($am->getDescendants($root)),
        	'elements' => $this->buildTree($root),
        ));
    }

    private function renderJSON($data)
    {
        header("Content-Type: application/json; charset=utf-8");

        echo CJSON::encode($data);

        foreach (Yii::app()->log->routes as $route) {
            if ($route instanceof CWebLogRoute) {
                $route->enabled = false; // disable any weblogroutes
            }
        }
        Yii::app()->end();
    }

    private function buildTree($name)
    {
        $childrenQuery = Yii::app()->db->createCommand()
        ->select('child')
        ->from('AuthItemChild')
        ->where('parent=:parent', array(':parent' => $name))
        ->queryColumn();

        if (empty($childrenQuery)) {
            return array(
                'name' => $name
            );
        }

        $children = array();

        foreach ($childrenQuery as $childName) {
            $children[] = $this->buildTree($childName);
        }

        return array(
            'name' => $name,
            'children' => $children
        );
    }
}
