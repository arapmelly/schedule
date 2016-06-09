<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 11/27/11
 * Time: 11:36 PM
 * To change this template use File | Settings | File Templates.
 */

class ScheduleController extends BaseController
{

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('onePerOne', 'checkIp', 'retweetAccounts'),
                'users' => array('*')
            ),
            array('deny', 'users' => array('?')),
        );
    }

    public function actionCheckIp()
    {
        $ipFile = './assets/last_ip';
        $lastIp = @file_get_contents($ipFile);
        $currentIp = file_get_contents('http://icanhazip.com/');
        if ($currentIp !== false) {
            if ($lastIp != $currentIp) {
                $api = new TwitterApi(
                    TwitterApplication::getDefault(),
                    TwitterAccount::model()->findByAttributes(array('screen_name' => 'SkovorogovorinP')));
                if ($res = $api->statusesUpdate('Счастливые номера на сегодня ' . $currentIp)) {
                    file_put_contents($ipFile, $currentIp);
                    echo $currentIp;
                } else {
                    echo 'twitter error';
                    var_dump($api->getLastResponse());
                }
            } else
                echo 'same';
        } else
            echo 'icanhazip error';
    }

    public function actionOnePerOne()
    {
        if (!AppHelper::getAppActive()) {
            echo 'not active';
            return;
        }

        $users = User::model()->findAll();
        $posts = array();
        foreach ($users as $user)
        {
            $user->username;
            $accounts = $user->activeTwitterAccounts;
            foreach ($accounts as $account)
            {
                $account->screen_name;
                $variants = $account->getNextTweetVariants();
                if ($user->post($account, $variants)) {
                    $posts[] = array(
                        'account' => $account,
                        'variants' => $variants instanceof TweetVariantGroup ? $variants->tweetVariants : $variants);
                }
            }
        }

        $this->render('onePerOne', array('posts' => $posts));
    }

    public function actionInitOnePerOne()
    {
        $ta = TwitterAccount::model()->findAll();
        foreach ($ta as $t) {
            $t->randomlyDisable();
            var_dump($t->attributes);
        }
    }

    public function actionRetweetAccounts()
    {
        $accountsRetwitter = TwitterAccount::model()
            ->with(
            array(
                'user',
                'retweetTwitterAccounts' => array('with' => array(
                    'hashTagGroups'
                )),
                'retweetTwitterAccounts.tweetGroup'
            ))
            ->retwitter()
            ->findAll();
        foreach ($accountsRetwitter as $accountRetwitter)
        {
            $api = $accountRetwitter->user->getTwitterApiForAccount($accountRetwitter);
            foreach ($accountRetwitter->retweetTwitterAccounts as $accountToRetweet)
            {
                $tweetsToRetweet = array();
                $lastTweetId = $accountToRetweet->getLastRetweetedTweetId($accountRetwitter);
                if ($api->statusesUserTimeline($accountToRetweet->name, $lastTweetId, true)) {
                    $tweets = $api->getLastResponse();
                    if (empty($lastTweetId)) {
                        $tweetsToRetweet[] = array_shift($tweets);
                    } else {
                        $tweetsToRetweet = $tweets;
                    }
                }
                $tweetsToRetweet = array_reverse($tweetsToRetweet);

                $lastTweetId = null;
                foreach ($tweetsToRetweet as $tweetRt)
                {
                    $tweetId = $tweetRt['id_str'];
                    $tweet = new Tweet();
                    $tweet->tweet_group_id = $accountToRetweet->tweetGroup->id;
                    $tweet->text = $tweetRt['text'];
                    $tweet->save();
                    $hashTagGroup = $accountToRetweet->getNextActiveHashTagGroup();
                    if ($hashTagGroup) {
                        $variantGroup = $tweet->getNextVariantGroup($accountRetwitter, $hashTagGroup);
                        $res = $accountRetwitter->user->post($accountRetwitter, $variantGroup);
                    } else {
                        $res = $accountRetwitter->user->post($accountRetwitter, $tweet);
                    }
                    var_dump($accountRetwitter->user->getTwitterApiForAccount($accountRetwitter)->getLastCode(), $tweetRt);

                    if ($res) {
                        $lastTweetId = $tweetId;
                    } else {
                        if ($api->getLastCode() == 403) {
                            $lastTweetId = $tweetId;
                        } else {
                            break;
                        }
                    }
                }
                if ($lastTweetId)
                    $accountToRetweet->setLastRetweetedTweetId($accountRetwitter, $lastTweetId);
            }
        }


        $this->render('index');
    }

    public function actionSpeedRetweetAccounts()
    {
        $tags = array('#Россия', '#ПЖИВ', '#24dec', '#БП', '#белаялента', '#КПРФ', '#выборы', '#митинг', '#пр', '#Moscow', '#msk', '#политика', '#OccupyRussia', '#Москва');
        $needDate = strtotime('24-12-2011 00:00:00');

        $accountRetwitter = TwitterAccount::model()->findByAttributes(array('screen_name' => 'Stalingrad10'));
        $accountRetwitter2 = TwitterAccount::model()->findByAttributes(array('screen_name' => 'Stalingrad2012'));
        $api = new TwitterApi(TwitterApplication::getDefault(), $accountRetwitter);
        $apiArr = array($api, new TwitterApi(TwitterApplication::getDefault(), $accountRetwitter2));
        $apiIdx = 0;
        foreach ($accountRetwitter->retweetTwitterAccounts as $accountToRetweet)
        {
            $tweetsToRetweet = array();
            if ($api->statusesUserTimeline($accountToRetweet->name, $accountToRetweet->getLastRetweetedTweetId($accountRetwitter), true)) {
                $tweets = $api->getLastResponse();
                foreach ($tweets as $tweet)
                {
                    if (strtotime($tweet['created_at']) >= $needDate) {
                        $tweetsToRetweet[] = $tweet;
                    }
                }
            }
            $tweetsToRetweet = array_reverse($tweetsToRetweet);

            $lastTweetId = null;
            foreach ($tweetsToRetweet as $tweet)
            {
                $variants = self::getAllTagsVariants($tweet['text'], $tags);
                var_dump($variants);
                foreach ($variants as $v)
                {
                    $api = $apiArr[$apiIdx % 2];
                    $api->statusesUpdate($v);
                    var_dump($api->getLastCode());
                    $apiIdx++;
                    sleep(rand(5, 8));
                }

                $tweetId = $tweet['id_str'];
                $res = $api->statusesRetweet($tweetId);
                var_dump($tweet['text'], $api->getLastResponse());
                if ($res) {
                    $lastTweetId = $tweetId;
                } else {
                    if ($api->getLastCode() == 403) {
                        $lastTweetId = $tweetId;
                    } else {
                        break;
                    }
                }
            }
            if ($lastTweetId)
                $accountToRetweet->setLastRetweetedTweetId($accountRetwitter, $lastTweetId);
        }

        $this->render('index');
    }


    public static function getAllTagsVariants($text, $tags)
    {
        $variants = array();
        $tweetVariant = $text;
        foreach ($tags as $tag)
        {
            if (mb_strpos($text, $tag) === false) {
                $tmp = $tweetVariant . ' ' . $tag;
                if (Tweet::length($tmp) > Tweet::TW_LEN) {
                    $variants[] = $tweetVariant;
                    $tweetVariant = $text;
                } else {
                    $tweetVariant = $tmp;
                }
            }
        }
        if ($tweetVariant != $text) {
            $variants[] = $tweetVariant;
        }

        return array_unique($variants);
    }

}
