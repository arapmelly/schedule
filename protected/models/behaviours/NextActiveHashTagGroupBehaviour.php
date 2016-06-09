<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 1/22/12
 * Time: 8:50 PM
 * To change this template use File | Settings | File Templates.
 */
class NextActiveHashTagGroupBehaviour extends CBehavior
{

    public function getNextActiveHashTagGroup()
    {
        $owner = $this->getOwner();
        $hashTagGroups = $owner->activeHashTagGroups;
        if (empty($hashTagGroups))
            return false;
        $nextHashTagGroup = null;
        if ($owner->last_hash_tag_group_id === null) {
            $nextHashTagGroup = $hashTagGroups[0];
        } else {
            $group = current($hashTagGroups);
            while ($group)
            {
                if ($group->id == $owner->last_hash_tag_group_id) {
                    break;
                }
                $group = next($hashTagGroups);
            }
            $nextHashTagGroup = next($hashTagGroups);
            if (empty($nextHashTagGroup))
                $nextHashTagGroup = $hashTagGroups[0];
        }
        if ($owner->last_hash_tag_group_id != $nextHashTagGroup->id) {
            $owner->last_hash_tag_group_id = $nextHashTagGroup->id;
            $owner->save();
        }

        return $nextHashTagGroup;
    }
}
