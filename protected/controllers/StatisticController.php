<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 1/24/12
 * Time: 11:40 AM
 * To change this template use File | Settings | File Templates.
 */
class StatisticController extends BaseController
{

    public function actionHistory()
    {
        $criteria = new CDbCriteria();
        if (!$this->getUser()->isAdmin()) {
            $criteria->compare('user_id', $this->userId());
        }
        $criteria->order = 'screen_name ASC';
        $accounts = TwitterAccount::model()->findAll($criteria);
        $this->render('history', array('accounts' => $accounts));
    }

    public function actionAccountHistory($account = null, $from = null, $to =  null)
    {
        $userId = null;
        if (empty($account) && !$this->getUser()->isAdmin())
            $userId = $this->userId();
        $history = UserHelper::getPostedHistory($userId, $account, $from, $to);
        echo CJSON::encode($history);
    }

    public function actionHistoryGraphic()
    {
        $history = UserHelper::getPostedHistoryByDate($this->userId());
        $this->render('history_graphic', array('history' => $history));
    }

    public function actionAllHistoryGraphic()
    {
        $history = UserHelper::getPostedHistoryByDate();
        $this->render('history_graphic', array('history' => $history));
    }

}
