<?php

class TwitterAccountController extends BaseController
{

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new TwitterAccount('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['TwitterAccount']))
            $model->attributes = $_GET['TwitterAccount'];

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    public function actionTwitterAccountHasGroup()
    {
        $this->render('twitterAccountHasGroup');
    }

    public function actionFollowEachOther()
    {
        $accounts = TwitterAccount::model()->findAll();
        foreach ($accounts as $accFollower)
        {
            foreach ($accounts as $acc)
            {
                if ($accFollower == $acc) continue;
                if ($accFollower->getFriendshipStatus($acc)) continue;
                $api = new TwitterApi(TwitterApplication::getDefault(), $accFollower);
                if ($api->friendshipsCreate($acc->twitter_id)) {
                    $accFollower->setFriendshipStatus($acc, true);
                    echo $accFollower->screen_name . ' -----> ' . $acc->screen_name . '<br/>';
                } else {
                    echo $accFollower->screen_name . ' -----X ' . $acc->screen_name . '<br/>';
                }
                sleep(rand(3, 5));
            }
        }
    }

    public function actionCheckFriendship()
    {
        $accounts = TwitterAccount::model()->findAll();
        foreach ($accounts as $accFollower)
        {
            foreach ($accounts as $acc)
            {
                if ($accFollower == $acc) continue;
                if (is_null($accFollower->getFriendshipStatus($acc))) {
                    $friendship = TwitterApi::friendshipsShow($accFollower->twitter_id, $acc->twitter_id);
                    if (!isset($friendship['relationship'])) return;
                    $accFollower->setFriendshipStatus($acc, $friendship['relationship']['source']['following']);
                    $acc->setFriendshipStatus($accFollower, $friendship['relationship']['target']['following']);
                }
            }
        }
    }

    public function actionTwitterAccountHasGroups($id)
    {
        if (isset($_POST['hasModels'])) {
            $account = TwitterAccount::model()->findByPk($id);
            if ($account->user_id == $this->userId()) {
                $account->deleteAllTweetGroups();
                if (is_array($_POST['hasModels']))
                    foreach ($_POST['hasModels'] as $model)
                    {
                        $account->addTweetGroup($model);
                    }
            }
        }
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = TwitterAccount::model()->findByPk($id);

        if ($model && isset($_POST['TwitterAccount'])) {
            $model->attributes = $_POST['TwitterAccount'];
            $model->save();
            $this->message('Account updated');
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        if (Yii::app()->request->isPostRequest) {
            $model = TwitterAccount::model()->findByPk($id);
            if ($model && $model->delete()) {
                $this->message('Account deleted - ' . $model->screen_name);
                $this->redirect(array('admin'));
                return;
            }
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }
}
