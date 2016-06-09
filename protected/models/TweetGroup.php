<?php
/*
 * update tweet t, tweet_group_has_tweet tht
 set t.tweet_group_id = tht.tweet_group_id
 where t.id = tht.tweet_id*/
/**
 * This is the model class for table "tweet_group".
 *
 * The followings are the available columns in table 'tweet_group':
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property integer $is_active
 * @property string $created
 * @property integer $last_hash_tag_group_id
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Tweet[] $tweets
 * @property TwitterAccount[] $twitterAccounts
 * @property HashTagGroup[] $hashTagGroups
 * @property HashTagGroup[] $activeHashTagGroups
 * @property integer $lessPostedTweetTimes
 */
class TweetGroup extends BaseActiveRecord
{
    const DEFAULT_NAME = 'Default';

    /**
     * Returns the static model of the specified AR class.
     * @return TweetGroup the static model class
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
        return 'tweet_group';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, name, created', 'required'),
            array('user_id, is_active, last_hash_tag_group_id', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 45),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, name, is_active, created', 'safe', 'on' => 'search'),
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
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'tweets' => array(self::HAS_MANY, 'Tweet', 'tweet_group_id'),
            'lessPostedTweetTimes' => array(self::STAT, 'Tweet', 'tweet_group_id', 'select' => 'MIN(times)'),
            'hashTagGroups' => array(self::MANY_MANY, 'HashTagGroup', 'tweet_group_use_hash_tag_group(tweet_group_id, hash_tag_group_id)'),
            'activeHashTagGroups' => array(self::MANY_MANY, 'HashTagGroup', 'tweet_group_use_hash_tag_group(tweet_group_id, hash_tag_group_id)',
                'condition' => 'is_active = 1'),
            'twitterAccounts' => array(self::MANY_MANY, 'TwitterAccount', 'twitter_account_post_tweet_group(tweet_group_id, twitter_account_id)'),
        );
    }

    public function init()
    {
        $this->attachBehavior('nextHashTagGroup', array('class' => 'NextActiveHashTagGroupBehaviour'));
        parent::init();
    }

    protected function beforeDelete()
    {
        Yii::app()->db->createCommand()
            ->delete('twitter_account_post_tweet_group', 'tweet_group_id = :tweet_group_id', array(':tweet_group_id' => $this->id));
        Yii::app()->db->createCommand()
            ->delete('tweet_group_use_hash_tag_group', 'tweet_group_id = :tweet_group_id', array(':tweet_group_id' => $this->id));

        $tweets = $this->tweets;
        $transaction = Yii::app()->db->beginTransaction();
        foreach ($tweets as $tweet)
        {
            $tweet->delete();
        }
        $transaction->commit();
        return parent::beforeDelete();
    }

    public function createTweet($tweetText)
    {
        if (Tweet::model()->findByAttributes(array('text' => $tweetText, 'tweet_group_id' => $this->id))) {
            return false;
        }
        $tweet = new Tweet();
        $tweet->tweet_group_id = $this->id;
        $tweet->text = $tweetText;
        $tweet->save();

        return $tweet;
    }

    public function addHashTagGroup($hashTagGroup)
    {
        if ($this->hasHashTagGroup($hashTagGroup))
            return false;

        $hashTagGroupId = $hashTagGroup instanceof HashTagGroup ? $hashTagGroup->id : $hashTagGroup;
        Yii::app()->db->createCommand()->insert('tweet_group_use_hash_tag_group', array('tweet_group_id' => $this->id, 'hash_tag_group_id' => $hashTagGroupId));
        return true;
    }

    public function hasHashTagGroup($hashTagGroup)
    {
        $hashTagGroupId = $hashTagGroup instanceof HashTagGroup ? $hashTagGroup->id : $hashTagGroup;
        $criteria = new CDbCriteria();
        $criteria->join = 'JOIN tweet_group_use_hash_tag_group ON tweet_group_id = t.id';
        $criteria->compare('tweet_group_id', $this->id);
        $criteria->compare('hash_tag_group_id', $hashTagGroupId);
        if (TweetGroup::model()->find($criteria)) {
            return true;
        }
        return false;
    }

    public function deleteAllHashTagGroups()
    {
        Yii::app()->db->createCommand()
            ->delete('tweet_group_use_hash_tag_group', 'tweet_group_id=' . $this->id);
        return true;
    }

    /**
     * @return Tweet
     */
    public function getNextActiveTweet()
    {
        $nextTweet = Tweet::model()->notPosted()->fromGroup($this->id)->find();
        if ($nextTweet) {
            return $nextTweet;
        } else {
            $nextTweet = Tweet::model()
                ->fromGroup($this->id)
                ->find(array('condition' => 'is_active = 1', 'order' => 'times ASC'));
            if ($nextTweet)
                return $nextTweet;
            else
                return false;
        }
    }

    /**
     * @return TweetVariant
     */
    public function getNextTweetVariant(TwitterAccount $twitterAccount)
    {
        $nextTweet = $this->getNextActiveTweet();
        $nextVariant = $nextTweet->getNextVariant($twitterAccount);

        return $nextVariant;
    }

    /**
     * @return TweetVariant
     */
    public function getNextTweetVariants(TwitterAccount $twitterAccount)
    {
        $hashTagGroup = $this->getNextActiveHashTagGroup();
        if (empty($hashTagGroup))
            return $this->getNextTweetVariant($twitterAccount);

        $nextTweet = $this->getNextActiveTweet();
        $variantsGroup = $nextTweet->getNextVariantGroup($twitterAccount, $hashTagGroup);

        return $variantsGroup;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'name' => 'Name',
            'is_active' => 'Is Active',
            'created' => 'Created',
            'last_hash_tag_group_id' => 'Last Hash Tag Group',
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
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('is_active', $this->is_active);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('last_hash_tag_group_id', $this->last_hash_tag_group_id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
} 