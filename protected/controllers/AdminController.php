<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 1/23/12
 * Time: 9:42 PM
 * To change this template use File | Settings | File Templates.
 */
class AdminController extends BackendController
{
    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionUpdateApplication($id)
    {
        $model = TwitterApplication::model()->findByPk($id);

        if ($model && isset($_POST['TwitterApplication'])) {
            $model->attributes = $_POST['TwitterApplication'];
            $model->save();
            $this->message('Twitter Application updated');
        }

        $this->render('updateApplication', array(
            'model' => $model,
        ));
    }

    public function actionToggleApplicationActive()
    {
        $status = AppHelper::getAppActive();
        AppHelper::getAppActive(!$status);
        if ($status) {
            $this->message('Application deactivated', self::MSG_ERROR);
        } else {
            $this->message('Application activated');
        }
        $this->redirect(array('index'));
    }

}
