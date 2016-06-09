<?php

class HashTagController extends BaseController
{
    public function actionCreate($group_id)
    {
        $group = HashTagGroup::model()->findByPk($group_id);

        if (isset($_POST['HashTag']) && $group && $group->user_id = $this->userId()) {
            $hashTag = $group->createHashTag($_POST['HashTag']['text']);
            if ($hashTag) {
                echo CJSON::encode($hashTag);
                return;
            }
        }
        throw new CHttpException(400);
    }

    public function actionDelete($id)
    {
        $tag = HashTag::model()->findByPk($id);

        if ($tag && $tag->hashTagGroup->user_id == $this->userId()) {
            $tag->delete();
            return;
        }
        throw new CHttpException(400);
    }
}
