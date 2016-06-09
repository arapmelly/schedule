<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 12/11/11
 * Time: 2:11 PM
 * To change this template use File | Settings | File Templates.
 */
class TweetGroupHelper
{
    public static function getUserGroupsWithTweets()
    {
        $groups = TweetGroup::model()->with('tweets')->findAllByAttributes(array('user_id' => User::current()->id));
        $groupsData = array();
        foreach ($groups as $gr)
        {
            $groupData = $gr->attributes;
            $groupData['tweets'] = array();
            foreach ($gr->tweets as $tw)
            {
                $groupData['tweets'][] = $tw->attributes;
            }
            $groupsData[] = $groupData;
        }
        return $groupsData;
    }
}
