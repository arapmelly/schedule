<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 1/18/12
 * Time: 7:02 PM
 * To change this template use File | Settings | File Templates.
 */
class CrudHelper
{

    static function buildRelationLinks($model, $id)
    {
        $relations = $model->getMetaData()->relations;
        $links = array();
        foreach ($relations as $name => $relation) {
            if($relation instanceof CStatRelation) continue;

            $link = CHtml::link($name, Yii::app()->createUrl(
                    lcfirst($relation->className) . '/list', array('ownerClass' => get_class($model), 'ownerId' => $id, 'relationName' => $name))
            );
            $links[] = $link;
        }
        return $links;
    }

    static function modelSearchAttributes(CActiveRecord $model)
    {
        $scenario = $model->getScenario();
        $model->setScenario('search');
        $searchValidator = null;
        foreach ($model->getValidators() as $validator)
        {
            if (isset($validator->on['search']) && $validator instanceof CSafeValidator) {
                $searchValidator = $validator;
                break;
            }
        }
        $model->setScenario($scenario);
        return $searchValidator->attributes;
    }
}
