<?php

class HashTagGroupController extends BaseController
{

    public function actionIndex()
    {
        $hashTagGroups = HashTagGroup::model()->findAllByAttributes(array('user_id' => $this->userId()));
        $this->render('index', array('newGroup' => new HashTagGroup(), 'hashTagGroups' => $hashTagGroups));
    }

    public function actionHashTagList($id)
    {
        echo CJSON::encode(HashTagGroup::model()->with('hashTags')->findByPk($id)->hashTags);
    }

    public function actionDelete($id)
    {
        $group = HashTagGroup::model()->findByPk($id);
        if ($group && $group->user_id == $this->userId() && $group->delete()) {
            return;
        }
        throw new CHttpException(400);
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new HashTagGroup();

        if (isset($_POST['HashTagGroup'])) {
            if (!HashTagGroup::model()->findAllByAttributes(array('name' => $_POST['HashTagGroup']['name'], 'user_id' => $this->userId()))) {
                $_POST['HashTagGroup']['user_id'] = $this->userId();
                $model->attributes = $_POST['HashTagGroup'];
                if ($model->save()) {
                    $this->message('Hash tag group created ');
                }
            } else {
                $this->message('Hash tag group exists', self::MSG_ERROR);
            }
        }

        $this->redirect('index');
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new HashTagGroup('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['HashTagGroup']))
            $model->attributes = $_GET['HashTagGroup'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }
}
