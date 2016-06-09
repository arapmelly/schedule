<?php

class TweetController extends BaseController
{
    public $defaultAction = 'createTweets';

    public function actionCreateTweets()
    {
        $importedTweets = array();

        if (isset($_POST['tweet_group_id'])) {
            $group = TweetGroup::model()->with('lessPostedTweetTimes', 'tweets')->findByPk($_POST['tweet_group_id']);
            if ($group && !is_null($file = CUploadedFile::getInstanceByName('tweets'))) {
                $importedTweets = Tweet::importTweetsFromFile($file->getTempName(), $group);
                if (count($importedTweets) > 0) {
                    $this->message('Created ' . count($importedTweets) . ' tweets.');
                }
                else {
                    $this->message('All tweets from file "' . $file->getName() . '" exists.', self::MSG_WARNING);
                }
            } else {
                $this->message('Select group.', self::MSG_ERROR);
            }
        }

        $groups = $this->getUser()->groups;
        $groupsOptions = array();
        $groupsOptions['default'] = '---------';
        foreach ($groups as $g)
        {
            $groupsOptions[$g->id] = $g->name;
        }
        $this->render('create_tweets', array('groups' => $groupsOptions, 'tweet' => new Tweet(), 'importedTweets' => $importedTweets));
    }

    public function actionDelete($id)
    {
        if (Yii::app()->request->getIsAjaxRequest()) {
            $model = Tweet::model()->with('tweetGroup')->findByPk($id);
            if ($model && $model->tweetGroup->user_id == $this->userId()) {
                $model->delete();
                return;
            }
        }
        throw new CHttpException(400);
    }

    public function actionCreate($group_id)
    {
        if (Yii::app()->request->getIsAjaxRequest()) {
            $group = TweetGroup::model()->with('lessPostedTweetTimes')->findByPk($group_id);

            if (isset($_POST['Tweet']) && $group && $group->user_id == $this->userId()) {

                if (!Tweet::model()->findByAttributes(array('text' => $_POST['Tweet']['text'], 'tweet_group_id' => $group->id))) {
                    $_POST['Tweet']['tweet_group_id'] = $group->id;
                    $_POST['Tweet']['times'] = $group->lessPostedTweetTimes ? $group->lessPostedTweetTimes : 0;
                    $tweet = new Tweet();
                    $tweet->attributes = $_POST['Tweet'];
                    if ($tweet->save()) {
                        echo CJSON::encode($tweet);
                        return;
                    }
                }
            }
        }
        throw new CHttpException(400);
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = Tweet::model()->findByPk($id);

        if (isset($_POST['Tweet'])) {
            $model->attributes = $_POST['Tweet'];
            if ($model->save()) {
                $this->message('Saved');
            } else {
                $this->message('Not saved', self::MSG_ERROR);
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }
}
