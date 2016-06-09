<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/11/11
 * Time: 2:09 PM
 * To change this template use File | Settings | File Templates.
 */
class TweetGroupController extends BaseController
{
    public function actionIndex()
    {
        $tweetGroups = TweetGroup::model()->findAllByAttributes(array('user_id' => $this->userId()));
        $this->render('index', array('tweetGroups' => $tweetGroups));
    }

    public function actionTweetList($id)
    {
        echo CJSON::encode(TweetGroup::model()->with('tweets')->findByPk($id)->tweets);
    }

    public function actionDelete($id)
    {
        if (Yii::app()->request->getIsAjaxRequest()) {
            $tweetGroup = TweetGroup::model()->findByPk($id);
            if ($tweetGroup && $tweetGroup->user_id == $this->userId() && $tweetGroup->delete()) {
                return;
            }
        }
        throw new CHttpException(400);
    }

    public function actionGetHashTagGroups($id)
    {
        $user = User::model()->with('hashTagGroups', 'hashTagGroups.tweetGroups')->findByPk($this->userId());
        $hashTagGroupsData = array();
        foreach ($user->hashTagGroups as $hashTagGroup)
        {
            $hashTagGroupData = $hashTagGroup->attributes;
            foreach ($hashTagGroup->tweetGroups as $tg)
            {
                if ($tg->id == $id) {
                    $hashTagGroupData['has'] = true;
                    break;
                }
            }
            $hashTagGroupsData[] = $hashTagGroupData;
        }

        echo CJSON::encode($hashTagGroupsData);
    }

    public function actionTweetGroupHasHashTagGroups($id)
    {
        $tweetGroup = TweetGroup::model()->findByPk($id);

        if ($tweetGroup && !empty($_POST['hasModels'])) {
            $tweetGroup->deleteAllHashTagGroups();
            if (is_array($_POST['hasModels']))
                foreach ($_POST['hasModels'] as $gr)
                {
                    $tweetGroup->addHashTagGroup($gr);
                }
        }
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        if (isset($_POST['TweetGroup'])) {
            if (!TweetGroup::model()->findAllByAttributes(array('name' => $_POST['TweetGroup']['name'], 'user_id' => $this->userId()))) {
                $_POST['TweetGroup']['user_id'] = $this->userId();
                $tweetGroup = new TweetGroup();
                $tweetGroup->attributes = $_POST['TweetGroup'];
                if ($tweetGroup->save()) {
                    $this->message('Successfully create group');
                } else {
                    $this->message('Group not created', 'error');
                }
            }
        }
        $this->redirect('index');
    }

}
