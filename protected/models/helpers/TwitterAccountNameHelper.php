<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/11/11
 * Time: 7:56 PM
 * To change this template use File | Settings | File Templates.
 */
class TwitterAccountNameHelper
{

    public static function getAccountNames()
    {
        $accountNames = TwitterAccountName::model()->with('twitterAccounts', 'hashTagGroups')->findAllByAttributes(array('user_id' => User::current()->id));
        $accountNamesData = array();
        foreach ($accountNames as $accName)
        {
            $accountNameData = $accName->attributes;
            $accountNameData['hashTagGroups'] = array();
            foreach ($accName->hashTagGroups as $gr)
            {
                $accountNameData['hashTagGroups'][] = $gr->attributes;
            }
            $accountNamesData[] = $accountNameData;
        }
        return $accountNamesData;
    }

    public static function getHashTagGroups()
    {
        $groups = HashTagGroup::model()->findAllByAttributes(array('user_id' => User::current()->id));
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
