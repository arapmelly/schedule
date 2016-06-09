<?php

/**
 * This is the model class for table "twitter_account_name".
 *
 * The followings are the available columns in table 'twitter_account_name':
 * @property integer $id
 * @property integer $user_id
 * @property integer $tweet_group_id
 * @property string $name
 * @property int $last_hash_tag_group_id
 *
 * The followings are the available model relations:
 * @property TwitterAccount[] $twitterAccounts
 * @property HashTagGroup[] $hashTagGroups
 * @property TweetGroup $tweetGroup
 */
class TwitterAccountName extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @return TwitterAccountName the static model class
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
        return 'twitter_account_name';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, name', 'required'),
            array('user_id, last_hash_tag_group_id, tweet_group_id', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name', 'safe', 'on' => 'search'),
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
            'twitterAccounts' => array(self::MANY_MANY, 'TwitterAccount', 'retweet_twitter_account(twitter_account_name_id, twitter_account_id)'),
            'hashTagGroups' => array(self::MANY_MANY, 'HashTagGroup', 'twitter_account_name_use_hash_tag_group(twitter_account_name_id, hash_tag_group_id)'),
            'activeHashTagGroups' => array(self::MANY_MANY, 'HashTagGroup', 'twitter_account_name_use_hash_tag_group(twitter_account_name_id, hash_tag_group_id)',
                'condition' => 'activeHashTagGroups.is_active = 1'),
            'tweetGroup' => array(self::BELONGS_TO, 'TweetGroup', 'tweet_group_id')
        );
    }

    public function init()
    {
        $this->attachBehavior('nextHashTagGroup', array('class' => 'NextActiveHashTagGroupBehaviour'));
        $this->onAfterSave = array($this, 'afterFirstSave');
        parent::init();
    }

    protected function beforeDelete()
    {
        $this->deleteAllHashTagGroups();
        $this->deleteAllTwitterAccounts();

        return parent::beforeDelete();
    }

    protected function afterFirstSave()
    {
        if ($this->getIsNewRecord()) {
            $this->tweetGroup = new TweetGroup();
            $this->tweetGroup->name = "[$this->name]";
            $this->tweetGroup->user_id = $this->user_id;
            if ($this->tweetGroup->save()) {
                Yii::app()->db->createCommand()
                    ->update($this->tableName(), array('tweet_group_id' => $this->tweetGroup->id), "id = $this->id");
            }
        }
    }

    public function setLastRetweetedTweetId(TwitterAccount $account, $id)
    {
        Yii::app()->db->createCommand()->update('retweet_twitter_account',
            array('last_retweeted_tweet_id' => $id),
            'twitter_account_id = :twitter_account_id and twitter_account_name_id = :twitter_account_name_id',
            array('twitter_account_id' => $account->id, 'twitter_account_name_id' => $this->id));
    }

    public function getLastRetweetedTweetId(TwitterAccount $account)
    {
        $res = Yii::app()->db->createCommand()
            ->select('last_retweeted_tweet_id')
            ->from('retweet_twitter_account')
            ->where('twitter_account_id = :twitter_account_id and twitter_account_name_id = :twitter_account_name_id',
            array('twitter_account_id' => $account->id, 'twitter_account_name_id' => $this->id))
            ->queryRow();

        return @$res['last_retweeted_tweet_id'];
    }

    public function addHashTagGroup($group)
    {
        $groupId = $group instanceof HashTagGroup ? $group->id : $group;
        Yii::app()->db->createCommand()
            ->insert('twitter_account_name_use_hash_tag_group',
            array('twitter_account_name_id' => $this->id, 'hash_tag_group_id' => $groupId));
    }

    public function deleteAllHashTagGroups()
    {
        Yii::app()->db->createCommand()
            ->delete('twitter_account_name_use_hash_tag_group', 'twitter_account_name_id=' . $this->id);
        return true;
    }

    public function deleteAllTwitterAccounts()
    {
        Yii::app()->db->createCommand()
            ->delete('retweet_twitter_account', 'twitter_account_name_id=' . $this->id);
        return true;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
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
        $criteria->compare('name', $this->name, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
}