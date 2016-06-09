<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/9/11
 * Time: 12:53 PM
 * To change this template use File | Settings | File Templates.
 */
class UserHelper
{
    public static function getPostedHistory($userId = null, $twitterAccountId = null, $from = null, $to = null)
    {
        $query = Yii::app()->db->createCommand()
            ->select('ta.screen_name, DATE_FORMAT(pv.created, \'%d-%m-%Y %H:%i:%s\') as created,  pv.http_code, pv.response, pv.tweet_variant_id, pv.tweet_variant_group_id, tv.tweet_id, tv.text')
            ->from('posted_variant pv')
            ->leftJoin('tweet_variant tv', 'tv.id = pv.tweet_variant_id')
            ->leftJoin('twitter_account ta', 'ta.id = pv.twitter_account_id')
            ->order('pv.created desc');


        $params = array();
        $where = array('and');
        if (!empty($userId)) {
            $where[] = 'pv.user_id = :user_id';
            $params[':user_id'] = $userId;
        }
        if (!empty($twitterAccountId)) {
            $where[] = 'pv.twitter_account_id = :twitter_account_id';
            $params[':twitter_account_id'] = $twitterAccountId;
        }
        if (!empty($from)) {
            $from = doubleval($from) / 1000;
            $where[] = 'pv.created >= :from';
            $params[':from'] = AppHelper::mysqlDate($from, false);
        }
        if (!empty($to)) {
            $to = doubleval($to) / 1000 + 60 * 60 * 24;
            $where[] = 'pv.created <= :to';
            $params[':to'] = AppHelper::mysqlDate($to, false);
        }
        if (count($where) > 1)
            $query->where($where, $params);

        if ($from && $to && $to - $from <= 60 * 60 * 24) {
            $query->limit(300);
        } else {
            $query->limit(100);
        }

        $result = $query->queryAll();

        return $result;
    }

    public static function getPostedHistoryByDate($userId = null, $twitterAccountId = null)
    {
        $query = Yii::app()->db->createCommand()
            ->select('DATE_FORMAT(pv.created, \'%Y-%m-%d\') as date, count(*) as times')
            ->from('posted_variant pv')
            ->group('date')
            ->order('date');

        if (!empty($userId) || !empty($twitterAccountId)) {
            $query->rightJoin('twitter_account ta', 'pv.twitter_account_id = ta.id');
        }
        $params = array();
        $where = array('and', 'http_code = 200');
        if (!empty($userId)) {
            $where[] = 'ta.user_id = :user_id';
            $params[':user_id'] = $userId;
        }
        if (!empty($twitterAccountId)) {
            $where[] = 'pv.twitter_account_id = :twitter_account_id';
            $params[':twitter_account_id'] = $twitterAccountId;
        }
        if (count($where) > 1)
            $query->where($where, $params);
        $result = $query->queryAll();

        $history = array();
        foreach ($result as $row)
        {
            $history[] = array(strtotime($row['date']) * 1000, intval($row['times']));
        }
        return $history;
    }


}
