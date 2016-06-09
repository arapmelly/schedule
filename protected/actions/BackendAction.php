<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 1/18/12
 * Time: 12:48 PM
 * To change this template use File | Settings | File Templates.
 */
abstract class BackendAction extends CAction
{
    private $_modelName;
    private $_view;

    /**
     * Упрощенная переадресация по действиям контроллера
     * По-умолчанию переходим на основное действие контроллера
     */
    public function redirect($actionId = null)
    {
        if ($actionId === null)
            $actionId = $this->controller->defaultAction;

        $this->controller->redirect(array($actionId));
    }

    /**
     * Рендер представление.
     * По-умолчанию рендерим одноименное представление
     */
    public function render($data, $return = false, $view = '')
    {
        $view = !empty($view) ? $view : '//crud/' . $this->view;
        return $this->controller->render($view, $data, $return);
    }

    /**
     * Возвращаем новую модель или пытаемся найти ранее
     * созданную запись, если известен id
     */
    public function getModel($scenario = 'insert')
    {
        if (($id = Yii::app()->request->getParam('id')) === null)
            $model = new $this->modelName($scenario);
        else if (($model = CActiveRecord::model($this->modelName)->resetScope()->findByPk($id)) === null)
            throw new CHttpException(404, Yii::t('base', 'The specified record cannot be found.'));

        return $model;
    }

    /**
     * Возвращает имя модели, с которой работает контроллер
     * По-умолчанию имя модели совпадает с именем контроллера
     */
    public function getModelName()
    {
        if ($this->_modelName === null)
            $this->_modelName = ucfirst($this->controller->id);

        return $this->_modelName;
    }

    public function setView($value)
    {
        $this->_view = $value;
    }

    public function getView()
    {
        if ($this->_view === null)
            $this->_view = $this->id;
        return $this->_view;
    }

    public function setModelName($value)
    {
        $this->_modelName = $value;
    }
}