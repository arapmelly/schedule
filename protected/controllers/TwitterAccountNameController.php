<?php

class TwitterAccountNameController extends BaseController
{
    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        //        $this->render('index', array('accountNames' => $accountNames, 'model' => new TwitterAccountName()));
    }

    public function actionTwitterAccountRetweetAccount()
    {
        $this->render('twitterAccountRetweetAccount');
    }

    public function actionAccountNameHasHashTagGroup()
    {
        $accountNames = TwitterAccountName::model()->findAllByAttributes(array('user_id' => $this->userId()));
        $this->render('accountNameHasHashTagGroup', array('accountNames' => $accountNames, 'model' => new TwitterAccountName()));
    }

    public function actionTwitterAccountHasAccountNames($id)
    {
        if (isset($_POST['hasModels'])) {
            $account = TwitterAccount::model()->findByPk($id);
            if ($account->user_id == $this->userId()) {
                $account->deleteAllAccountNames();
                if (is_array($_POST['hasModels']))
                    foreach ($_POST['hasModels'] as $model)
                    {
                        $account->addRetweetedAccount($model);
                    }
            }
        }
    }

    public function actionAccountNameHasHashTagGroups($id)
    {
        if (isset($_POST['hasModels'])) {
            $account = TwitterAccountName::model()->findByPk($id);
            if ($account->user_id == $this->userId()) {
                $account->deleteAllHashTagGroups();
                if (is_array($_POST['hasModels']))
                    foreach ($_POST['hasModels'] as $model)
                    {
                        $account->addHashTagGroup($model);
                    }
            }
        }
    }

    public function actionCreate()
    {
        $model = new TwitterAccountName();
        if (isset($_POST['TwitterAccountName'])) {
            if (!TwitterAccountName::model()->findAllByAttributes(array('name' => $_POST['TwitterAccountName']['name'], 'user_id' => $this->userId()))) {
                $model->attributes = $_POST['TwitterAccountName'];
                $model->user_id = $this->userId();
                if ($model->save()) {
                    $this->message('Created');
                }
            } else {
                $this->message('Exists', self::MSG_ERROR);
            }
        }
        $this->redirect(Yii::app()->request->getUrlReferrer());
    }

    public function actionDelete($id)
    {
        if (Yii::app()->request->isPostRequest) {
            $model = TwitterAccountName::model()->findByPk($id);
            if ($model && $model->user_id == $this->userId())
                $model->delete();
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }


    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new TwitterAccountName('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['TwitterAccountName']))
            $model->attributes = $_GET['TwitterAccountName'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }
}
