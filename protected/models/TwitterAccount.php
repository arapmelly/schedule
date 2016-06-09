<?php

/**
 * This is the model class for table "twitter_account".
 *
 * The followings are the available columns in table 'twitter_account':
 * @property integer $id
 * @property integer $user_id
 * @property integer $twitter_id
 * @property string $screen_name
 * @property string $oauth_token
 * @property string $oauth_token_secret
 * @property string $account_data
 * @property integer $is_active
 * @property double $frequency
 * @property string $disabled_until
 * @property string $created
 * @property string @follow_relation
 * @property integer $last_group_id
 *
 * The followings are the available model relations:
 * @property PostedVariant[] $postedTweets
 * @property User[] $users
 * @property TweetGroup[] $tweetGroups
 * @property TweetGroup[] $activeTweetGroups
 */
class TwitterAccount extends BaseActiveRecord
{

    /**
     * Returns the static model of the specified AR class.
     * @return TwitterAccount the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'twitter_account';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('is_active', 'default', 'value' => 1),
            array('frequency', 'default', 'value' => 60),
            array('user_id, twitter_id, screen_name, disabled_until, created', 'required'),
            array('user_id, twitter_id, is_active, last_group_id, follow_relation', 'numerical', 'integerOnly' => true),
            array('frequency', 'numerical'),
            array('screen_name', 'length', 'max' => 100),
            array('oauth_token, oauth_token_secret', 'length', 'max' => 225),
            array('account_data', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('created, screen_name, is_active, disabled_until, created', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'postedTweets' => array(self::HAS_MANY, 'PostedVariant', 'twitter_account_id'),
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'tweetGroups' => array(self::MANY_MANY, 'TweetGroup', 'twitter_account_post_tweet_group(twitter_account_id, tweet_group_id)'),
            'activeTweetGroups' => array(self::MANY_MANY, 'TweetGroup', 'twitter_account_post_tweet_group(twitter_account_id, tweet_group_id)',
                'condition' => 'is_active = 1'),
            'retweetTwitterAccounts' => array(self::MANY_MANY, 'TwitterAccountName', 'retweet_twitter_account(twitter_account_name_id, twitter_account_id)'),
        );
    }

    public function scopes()
    {
        return array(
            'active' => array(
                'condition' => 'is_active = 1 and disabled_until < UTC_TIMESTAMP()',
                'limit' => 5,
            ),
        );
    }

    public function retwitter()
    {
        $criteria = new CDbCriteria();
        $criteria->join = 'join retweet_twitter_account on twitter_account_id = t.id';
        $criteria->condition = 't.is_active = 1';

        $this->getDbCriteria()->mergeWith($criteria);
        return $this;
    }

    protected function beforeDelete()
    {
        $this->deleteAllTweetGroups();
        $this->deleteAllAccountNames();

        return parent::beforeDelete();
    }

    protected function beforeValidate()
    {
        if (is_null($this->id))
            $this->disabled_until = AppHelper::mysqlDate();
        return parent::beforeValidate();
    }

    public function addTweetGroup($group)
    {
        $groupId = $group instanceof TweetGroup ? $group->id : $group;
        Yii::app()->db->createCommand()
            ->insert('twitter_account_post_tweet_group', array('twitter_account_id' => $this->id, 'tweet_group_id' => $groupId));
    }

    public function removeGroup($group)
    {
        $groupId = $group instanceof TweetGroup ? $group->id : $group;
        Yii::app()->db->createCommand()
            ->delete('twitter_account_post_tweet_group',
            'twitter_account_id =:twitter_account_id and tweet_group_id =:tweet_group_id ',
            array(':twitter_account_id' => $this->id, ':tweet_group_id' => $groupId));
    }

    public function deleteAllTweetGroups()
    {
        Yii::app()->db->createCommand()
            ->delete('twitter_account_post_tweet_group', 'twitter_account_id=' . $this->id);
        return true;
    }

    public function deleteAllAccountNames()
    {
        Yii::app()->db->createCommand()
            ->delete('retweet_twitter_account', 'twitter_account_id=' . $this->id);
        return true;
    }

    public function addRetweetedAccount($accountName)
    {
        $groupId = $accountName instanceof TwitterAccountName ? $accountName->id : $accountName;
        Yii::app()->db->createCommand()
            ->insert('retweet_twitter_account', array('twitter_account_id' => $this->id, 'twitter_account_name_id' => $groupId));
    }

    public static function createFromAccessToken($accessToken, User $user)
    {
        if (!isset($accessToken['user_id']))
            throw new Exception('Invalid params');

        $account = TwitterAccount::model()->findByAttributes(array('twitter_id' => $accessToken['user_id']));
        if (is_null($account))
            $account = new TwitterAccount();
        $account->user_id = $user->id;
        $account->twitter_id = $accessToken['user_id'];
        $account->is_active = 1;
        $account->screen_name = $accessToken['screen_name'];
        $account->oauth_token = $accessToken['oauth_token'];
        $account->oauth_token_secret = $accessToken['oauth_token_secret'];
        $account->save();

        return $account;
    }

    public function randomlyDisable($k = 1)
    {
        $k = max(1, $k);
        $frequency = $k * $this->frequency;
        $interval = $frequency / 4;
        $period = ($frequency + rand(0, $interval) - rand(0, $interval)) * 60;
        $this->disableFor($period);
    }

    public function disableFor($seconds)
    {
        $this->disabled_until = AppHelper::mysqlDate(time() + $seconds);
        $this->save();
    }

    /**
     * @return TweetGroup.
     */
    public function getNextActiveTweetGroup()
    {
        $tweetGroups = $this->activeTweetGroups;
        if (empty($tweetGroups))
            return false;
        $nextTweetGroup = null;
        if ($this->last_group_id === null) {
            $nextTweetGroup = $tweetGroups[0];
        } else {
            $group = current($tweetGroups);
            while ($group)
            {
                if ($group->id == $this->last_group_id) {
                    break;
                }
                $group = next($tweetGroups);
            }
            $nextTweetGroup = next($tweetGroups);
            if (empty($nextTweetGroup))
                $nextTweetGroup = $tweetGroups[0];
        }
        if ($this->last_group_id != $nextTweetGroup->id) {
            $this->last_group_id = $nextTweetGroup->id;
            $this->save(false, array('last_group_id'));
        }
        return $nextTweetGroup;
    }

    public function getNextTweetVariant()
    {
        $nextGroup = $this->getNextActiveTweetGroup();
        if (empty($nextGroup)) return false;

        $variant = $nextGroup->getNextTweetVariant($this);

        return $variant;
    }

    public function getNextTweetVariants()
    {
        $nextGroup = $this->getNextActiveTweetGroup();
        if (empty($nextGroup)) return false;

        $variants = $nextGroup->getNextTweetVariants($this);

        return $variants;
    }

    public function getPostedTweetIds()
    {
        $result = Yii::app()->db->createCommand()
            ->select('tweet_id')
            ->from(PostedVariant::model()->tableName())
            ->where('twitter_account_id = :twitter_account_id',
            array(':twitter_account_id' => $this->id))
            ->queryAll();
        $ids = array();
        foreach ($result as $row)
        {
            $ids[] = $row['tweet_id'];
        }
        return $ids;
    }

    public function getFriendshipStatus(TwitterAccount $ta)
    {
        $res = Yii::app()->db->createCommand()
            ->select('friends')
            ->from('twitter_account_friendship')
            ->where('first_twitter_account_id = :first_twitter_account_id and second_twitter_account_id=:second_twitter_account_id',
            array('first_twitter_account_id' => $this->id, 'second_twitter_account_id' => $ta->id))->queryRow();

        if ($res === false) return null;

        return $res['friends'] == 1;
    }

    public function setFriendshipStatus(TwitterAccount $ta, $status)
    {
        $friendship = $this->getFriendshipStatus($ta);
        if ($friendship === $status) return;

        if ($friendship === null) {
            Yii::app()->db->createCommand()->insert('twitter_account_friendship',
                array('first_twitter_account_id' => $this->id, 'second_twitter_account_id' => $ta->id, 'friends' => $status));
        } else {
            Yii::app()->db->createCommand()
                ->update('twitter_account_friendship', array('friends' => $status),
                'first_twitter_account_id = :first_twitter_account_id and second_twitter_account_id=:second_twitter_account_id',
                array('first_twitter_account_id' => $this->id, 'second_twitter_account_id' => $ta->id));
        }
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'twitter_id' => 'Twitter',
            'screen_name' => 'Screen Name',
            'oauth_token' => 'Oauth Token',
            'oauth_token_secret' => 'Oauth Token Secret',
            'account_data' => 'Account Data',
            'is_active' => 'Is Active',
            'frequency' => 'Frequency',
            'disabled_until' => 'Disabled Until',
            'created' => 'Created',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('twitter_id', $this->twitter_id);
        $criteria->compare('screen_name', $this->screen_name, true);
        $criteria->compare('oauth_token', $this->oauth_token, true);
        $criteria->compare('oauth_token_secret', $this->oauth_token_secret, true);
        $criteria->compare('account_data', $this->account_data, true);
        $criteria->compare('frequency', $this->frequency);
        $criteria->compare('is_active', $this->is_active);
        $criteria->compare('disabled_until', $this->disabled_until, true);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('user_id', Yii::app()->user->id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => 'created DESC',
            ),
        ));
    }
}