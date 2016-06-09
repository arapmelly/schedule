<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 11/27/11
 * Time: 9:12 PM
 * To change this template use File | Settings | File Templates.
 */

class BaseActiveRecord extends CActiveRecord
{

    protected function beforeValidate()
    {
        if (is_null($this->id))
            $this->created = AppHelper::mysqlDate();
        return parent::beforeValidate();
    }

    protected function beginTransaction()
    {
        $connection = Yii::app()->db;
        if ($connection->currentTransaction && $connection->currentTransaction->getActive()) {
            return false;
        } else
            return $connection->beginTransaction();
    }

//    protected function afterValidate()
//    {
//        if($this->hasErrors()){
//            throw new Exception(print_r($this->getErrors(), true));
//        }
//        return parent::afterValidate();
//    }

}
