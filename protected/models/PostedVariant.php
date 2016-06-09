<?php

/**
 * This is the model class for table "posted_variant".
 *
 * The followings are the available columns in table 'posted_variant':
 * @property integer $id
 * @property integer $user_id
 * @property integer $twitter_application_id
 * @property integer $twitter_account_id
 * @property integer $tweet_variant_group_id
 * @property integer $tweet_variant_id
 * @property string $http_code
 * @property string $response
 * @property string $created
 *
 * The followings are the available model relations:
 * @property TwitterAccount $twitterAccount
 * @property TwitterApplication $twitterApplication
 * @property User $user
 * @property TweetVariant $tweetVariant
 */
class PostedVariant extends BaseActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @return PostedVariant the static model class
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
        return 'posted_variant';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id ,twitter_application_id, twitter_account_id, tweet_variant_id, created', 'required'),
            array('user_id, twitter_application_id, twitter_account_id, tweet_variant_id, tweet_variant_group_id', 'numerical', 'integerOnly' => true),
            array('http_code', 'length', 'max' => 5),
            array('response', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, twitter_application_id, twitter_account_id, tweet_variant_id, http_code, response, created', 'safe', 'on' => 'search'),
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
            'twitterAccount' => array(self::BELONGS_TO, 'TwitterAccount', 'twitter_account_id'),
            'twitterApplication' => array(self::BELONGS_TO, 'TwitterApplication', 'twitter_application_id'),
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
            'tweetVariant' => array(self::BELONGS_TO, 'TweetVariant', 'tweet_variant_id'),
            'variantGroup' => array(self::BELONGS_TO, 'TweetVariantGroup', 'tweet_variant_group_id'),
        );
    }

    protected function beforeValidate()
    {
        //        if (!is_null($this->response))
        //            $this->response = serialize($this->response);
        return parent::beforeValidate();
    }

    protected function afterFind()
    {
        //        if (!is_null($this->response))
        //            $this->response = unserialize($this->response);
        return parent::afterFind();
    }

    public function handleNewPost(Event $event)
    {
        $params = $event->params;
        $postedTweet = new PostedVariant();
        $postedTweet->user_id = $params['user']->id;
        $postedTweet->twitter_application_id = $params['application']->id;
        $postedTweet->twitter_account_id = $params['account']->id;
        if (isset($params['tweetVariantGroup']))
            $postedTweet->tweet_variant_group_id = $params['tweetVariantGroup']->id;
        $postedTweet->tweet_variant_id = $params['variant']->id;
        $postedTweet->http_code = $params['http_code'];
        if (isset($params['response']['error']))
            $postedTweet->response = $params['response']['error'];
        $postedTweet->save();

        if($params['http_code'] == 401)
        {
            $params['account']->is_active = 0;
            $params['account']->save();
        }
    }

    public static function postedVariantIds($twitterAccountId, $tweetId)
    {
        $postedResult = Yii::app()->db->createCommand()
            ->select('tweet_variant_id')
            ->from('posted_variant pv')
            ->join('tweet_variant tv', 'pv.tweet_variant_id = tv.id')
            ->where('twitter_account_id = :twitter_account_id and tv.tweet_id = :tweet_id and pv.tweet_variant_group_id is NULL and pv.created > DATE_ADD(NOW(), INTERVAL -1 WEEK)',
            array('twitter_account_id' => $twitterAccountId, 'tweet_id' => $tweetId))
            ->queryAll();
        $postedVariantsIds = array();
        foreach ($postedResult as $res)
        {
            $postedVariantsIds[] = $res['tweet_variant_id'];
        }

        return $postedVariantsIds;
    }

    public static function postedGroupIds($twitterAccountId, $tweetId, $hashTagGroupId)
    {
        $postedResult = Yii::app()->db->createCommand()
            ->select('tweet_variant_group_id')
            ->from('posted_variant pv')
            ->join('tweet_variant_group vg', 'pv.tweet_variant_group_id = vg.id')
            ->where('twitter_account_id = :twitter_account_id and vg.tweet_id = :tweet_id and vg.hash_tag_group_id = :hash_tag_group_id and pv.created > DATE_ADD(NOW(), INTERVAL -1 WEEK)',
            array('twitter_account_id' => $twitterAccountId, 'tweet_id' => $tweetId, 'hash_tag_group_id' => $hashTagGroupId))
            ->group('tweet_variant_group_id')
            ->queryAll();
        $postedIds = array();
        foreach ($postedResult as $res)
        {
            $postedIds[] = $res['tweet_variant_group_id'];
        }

        return $postedIds;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'twitter_application_id' => 'Twitter Application',
            'twitter_account_id' => 'Twitter Account',
            'tweet_variant_id' => 'tweet_variant_id',
            'http_code' => 'Http Code',
            'response' => 'Response',
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
        $criteria->compare('twitter_application_id', $this->twitter_application_id);
        $criteria->compare('twitter_account_id', $this->twitter_account_id);
        $criteria->compare('tweet_variant_id', $this->tweet_variant_id);
        $criteria->compare('http_code', $this->http_code, true);
        $criteria->compare('response', $this->response, true);
        $criteria->compare('created', $this->created, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
} 