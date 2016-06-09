<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/11/11
 * Time: 7:56 PM
 * To change this template use File | Settings | File Templates.
 */
class TwitterAccountHelper
{

    public static function getAccounts()
    {
        $accounts = TwitterAccount::model()->with('tweetGroups', 'retweetTwitterAccounts')->findAllByAttributes(array('user_id' => User::current()->id));
        $accountsData = array();
        foreach ($accounts as $acc)
        {
            $accountData = $acc->attributes;
            $accountData['groups'] = array();
            foreach ($acc->tweetGroups as $gr)
            {
                $accountData['groups'][] = $gr->attributes;
            }
            $accountData['retweetAccounts'] = array();
            foreach ($acc->retweetTwitterAccounts as $names)
            {
                $accountData['retweetAccounts'][] = $names->attributes;
            }
            $accountsData[] = $accountData;
        }
        return $accountsData;
    }

    public static function getGroups()
    {
        $groups = TweetGroup::model()->findAllByAttributes(array('user_id' => User::current()->id));
        $data = array();
        foreach ($groups as $gr)
        {
            $data[] = $gr->attributes;
        }
        return $data;
    }

    public static function getRetweetAccounts()
    {
        $accountNames = TwitterAccountName::model()->findAllByAttributes(array('user_id' => User::current()->id));
        $data = array();
        foreach ($accountNames as $name)
        {
            $data[] = $name->attributes;
        }
        return $data;
    }

}
