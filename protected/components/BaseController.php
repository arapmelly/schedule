<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class BaseController extends CController
{
    const MSG_SUCCESS = 'success';
    const MSG_WARNING = 'warning';
    const MSG_ERROR = 'error';

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/column1';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();


    private $_modelName;


    public function getUser()
    {
        return User::current();
    }

    public function userId()
    {
        return Yii::app()->user->id;
    }

    public function message($message, $type = self::MSG_SUCCESS)
    {
        Yii::app()->user->setFlash($type, $message);
    }

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    public function accessRules()
    {
        return array(
            array('deny', 'users' => array('?')),
        );
    }

    public function getModelName()
    {
        if ($this->_modelName === null)
            $this->_modelName = ucfirst($this->id);

        return $this->_modelName;
    }
}