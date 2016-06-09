<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 1/18/12
 * Time: 12:49 PM
 * To change this template use File | Settings | File Templates.
 */
class ListAction extends BackendAction
{
    public function run($ownerId = null, $ownerClass = null, $relationName = null)
    {
        $model = $this->getModel('search');
        if (!empty($relationName) && !empty($ownerClass) && !empty($ownerId) && class_exists($ownerClass)) {
            $owner = $ownerClass::model()->findByPk($ownerId);
            if ($owner && key_exists($relationName, $owner->relations())) {
                $c = $owner->buildDbCriteriaForRelation($relationName);
                $model->getDbCriteria()->mergeWith($c);
            }
        }

        if (isset($_GET[$this->modelName]))
            $model->attributes = $_GET[$this->modelName];

        $this->render(array(
            'model' => $model
        ));
    }
}