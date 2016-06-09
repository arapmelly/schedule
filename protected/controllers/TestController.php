<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 11/29/11
 * Time: 10:51 PM
 * To change this template use File | Settings | File Templates.
 */
Yii::import('application.controllers.*');

class TestController extends BaseController
{

    public function actionIndex()
    {
//        AppHelper::getAppActive(false);
        $this->render('index');
    }

    public function actionNext()
    {
        $account = TwitterAccount::model()->findByAttributes(array('screen_name' => 'OWS74'));
        //        $acc = $account->getNextTweetVariant();
        $nextTweet = Tweet::model()->findByPk(943);
        $v = $nextTweet->getNextVariantGroup($account, HashTagGroup::model()->findByPk(6));
        var_dump($v->attributes);
        $this->getUser()->post($account, $v);
        $this->render('index');
    }

    public function actionPost()
    {
        $user = User::current();
        $account = TwitterAccount::model()->findByAttributes(array('screen_name' => 'SkovorogovorinP'));
        $i = 0;
        while ($variant = $account->getNextTweetVariant()) {
            var_dump($variant->attributes);
            $user->post($account, $variant);
            $i++;
            if ($i > 10)
                break;
        }
        $this->render('index');
    }

    public function actionVariants()
    {
        $users = User::model()->findAll();
        $posts = array();
        $break = false;
        while (!$break) {
            foreach ($users as $user)
            {
                $accounts = $user->twitterAccounts;
                foreach ($accounts as $account)
                {
                    $variant = Tweet::getNextRandomTweetVariant($user, $account);
                    if (!empty($variant)) {
                        if ($user->post($account, $variant))
                            $posts[] = array('account' => $account, 'tweet' => $variant);
                    } else {
                        $break = true;
                    }
                }
            }
        }
        $this->render('index');
    }
}
