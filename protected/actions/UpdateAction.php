<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 1/18/12
 * Time: 12:50 PM
 * To change this template use File | Settings | File Templates.
 */
class UpdateAction extends BackendAction
{
    public function run()
    {
        $model = $this->getModel();

        if (isset($_POST[$this->modelName])) {
            $model->attributes = $_POST[$this->modelName];

            if ($model->save())
                $this->redirect();
        }

        $this->render(array('model' => $model));
    }
}