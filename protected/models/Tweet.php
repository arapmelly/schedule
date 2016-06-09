<?php

/**
 * This is the model class for table "tweet".
 *
 * The followings are the available columns in table 'tweet':
 * @property integer $id
 * @property integer $tweet_group_id
 * @property string $text
 * @property integer $times
 * @property integer $is_active
 * @property string $created
 *
 * The followings are the available model relations:
 * @property PostedVariant[] $postedTweets
 * @property TweetGroup $tweetGroup
 * @property TweetVariantGroup[] $variantGroups
 * @property TweetVariant[] $variants
 * @property User[] $users
 */
class Tweet extends BaseActiveRecord
{
    const TW_LEN = 140;
    const TW_URL_LEN = 20;
    const ENC = 'utf8';
    const REGEX_URL = '/(?#Protocol)(?:(?:ht|f)tp(?:s?)\:\/\/|~\/|\/)?(?#Username:Password)(?:\w+:\w+@)?(?#Subdomains)(?:(?:[-\w]+\.)+(?#TopLevel Domains)(?:com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum|travel|[a-z]{2}))(?#Port)(?::[\d]{1,5})?(?#Directories)(?:(?:(?:\/(?:[-\w~!$+|.,=]|%[a-f\d]{2})+)+|\/)+|\?|#)?(?#Query)(?:(?:\?(?:[-\w~!$+|.,*:]|%[a-f\d{2}])+=?(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)(?:&(?:[-\w~!$+|.,*:]|%[a-f\d{2}])+=?(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)*)*(?#Anchor)(?:#(?:[-\w~!$+|.,*:=]|%[a-f\d]{2})*)?/';

    /**
     * Returns the static model of the specified AR class.
     * @return Tweet the static model class
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
        return 'tweet';
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
            array('text, created, tweet_group_id', 'required'),
            array('times, is_active, tweet_group_id', 'numerical', 'integerOnly' => true),
            array('text', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, text, times, created', 'safe', 'on' => 'search'),
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
            'postedTweets' => array(self::HAS_MANY, 'PostedVariant', 'tweet_id'),
            'variants' => array(self::HAS_MANY, 'TweetVariant', 'tweet_id'),
            'tweetGroup' => array(self::BELONGS_TO, 'TweetGroup', 'tweet_group_id'),
            'variantGroups' => array(self::HAS_MANY, 'TweetVariantGroup', 'tweet_id')
        );
    }

    public function notPosted($order = 'rand()')
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('is_active = 1');
        $criteria->join =
            'LEFT JOIN tweet_variant tv on t.id = tv.tweet_id ' .
                'LEFT JOIN posted_variant pv on tv.id = pv.id';
        $criteria->addCondition('pv.id is NULL');
        $criteria->order = $order;

        $this->getDbCriteria()->mergeWith($criteria);
        return $this;
    }

    public function fromGroup($groupId)
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.tweet_group_id = ' . $groupId);
        $criteria->order = 'rand()';

        $this->getDbCriteria()->mergeWith($criteria);
        return $this;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'text' => 'Text',
            'times' => 'Times',
            'is_active' => 'Is active',
            'created' => 'Created',
        );
    }

    /**
     * @return TweetVariant
     */
    public function getNextVariant(TwitterAccount $twitterAccount)
    {
        $postedVariantsIds = PostedVariant::postedVariantIds($twitterAccount->id, $this->id);
        $criteria = new CDbCriteria();
        $criteria->compare('tweet_id', $this->id);
        //        $criteria->join = 'JOIN posted_variant pv on t.id = pv.tweet_variant_id';
        $criteria->addCondition('tweet_variant_group_id is NULL');
        $variants = TweetVariant::model()->findAll($criteria);
        $variantsText = array();
        foreach ($variants as $v)
        {
            if (array_search($v->id, $postedVariantsIds) === false) {
                return $v;
            }
            $variantsText[] = $v->text;
        }
        $cs = new CharSubstitutor($this->text);
        $substitutions = $cs->getAllSubstitutionVariants();
        foreach ($substitutions as $sub)
        {
            if (array_search($sub, $variantsText) === false) {
                $nextVariant = new TweetVariant();
                $nextVariant->tweet_id = $this->id;
                $nextVariant->text = $sub;
                $nextVariant->save();
                return $nextVariant;
            }
        }
        foreach ($substitutions as $sub)
        {
            $mutating = new Mutating($sub);
            while (($nextVariantText = $mutating->getNextVariant()) !== false)
            {
                if (array_search($nextVariantText, $variantsText) === false) {
                    $nextVariant = new TweetVariant();
                    $nextVariant->tweet_id = $this->id;
                    $nextVariant->text = $nextVariantText;
                    $nextVariant->save();
                    return $nextVariant;
                }
            }
        }

        return false;
    }

    /**
     * @return TweetVariant
     */
    public function getNextVariantGroup(TwitterAccount $twitterAccount, HashTagGroup $hashTagGroup)
    {
        $postedGroupsIds = PostedVariant::postedGroupIds($twitterAccount->id, $this->id, $hashTagGroup->id);
        $variantGroups = TweetVariantGroup::model()->findAllByAttributes(array('tweet_id' => $this->id, 'hash_tag_group_id' => $hashTagGroup->id));
        $variantsText = array();
        foreach ($variantGroups as $group)
        {
            if (array_search($group->id, $postedGroupsIds) === false) {
                return $group;
            }
            foreach ($group->tweetVariants as $v)
            {
                $variantsText[] = $v->text;
            }
        }

        $tweetsWithTagPackage = Tag::variants($this->text, $hashTagGroup->getHashTags());
        $allTweetsVariantsByTagPackage = array();
        foreach ($tweetsWithTagPackage as $v)
        {
            $cs = new CharSubstitutor($v);
            $allTweetsVariantsByTagPackage[] = $cs->getAllSubstitutionVariants();
        }

        $variants = array();
        foreach ($allTweetsVariantsByTagPackage as $allTweetVariants)
        {
            $newVariant = null;
            foreach ($allTweetVariants as $nextSubstitution)
            {

                if (array_search($nextSubstitution, $variantsText) === false) {
                    $newVariant = $nextSubstitution;
                    break;
                }
            }
            //if all variants are posted apply Mutation
            foreach ($allTweetVariants as $nextSubstitution)
            {
                if ($newVariant) break;
                $mutating = new Mutating($nextSubstitution);
                while (($nextMutation = $mutating->getNextVariant()) !== false)
                {
                    if (array_search($nextMutation, $variantsText) === false) {
                        $newVariant = $nextMutation;
                        break;
                    }
                }
            }
            if ($newVariant) {
                $variants[] = $newVariant;
            }
        }

        if (!empty($variants)) {
            $variantGroup = new TweetVariantGroup();
            $variantGroup->tweet_id = $this->id;
            $variantGroup->hash_tag_group_id = $hashTagGroup->id;
            $variantGroup->save();
            $newVariants = array();
            $transaction = $this->beginTransaction();
            foreach ($variants as $v)
            {
                $newVariant = new TweetVariant();
                $newVariant->tweet_id = $this->id;
                $newVariant->text = $v;
                $newVariant->tweet_variant_group_id = $variantGroup->id;
                $newVariant->save();
                $newVariants[] = $newVariant;
            }
            $transaction->commit();
            $variantGroup->tweetVariants = $newVariants;
            return $variantGroup;
        }

        return false;
    }

    public static function crop($text)
    {
        $extra = 0;
        $realLength = mb_strlen($text, self::ENC);
        $twitterLength = Tweet::length($text);
        if ($twitterLength > self::TW_LEN) {
            $extra = $twitterLength - self::TW_LEN;
        }
        return mb_substr($text, 0, $realLength - $extra, self::ENC);
    }

    public static function length($text)
    {
        $length = mb_strlen($text, self::ENC);
        if (preg_match_all(self::REGEX_URL, $text, $matches)) {
            foreach ($matches[0] as $url)
            {
                $length = $length - mb_strlen($url, self::ENC) + self::TW_URL_LEN;
            }
        }
        return $length;
    }


    public static function linkAtEnd($text)
    {
        if (preg_match('/(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/', $text, $matches)) {
            if (mb_substr(
                $text,
                mb_strlen($text, self::ENC) - mb_strlen($matches[0], self::ENC) - 1,
                mb_strlen($matches[0], self::ENC))
                == $matches[0]
            )
                return true;
        }
        return false;
    }

    public static function importTweetsFromFile($fileName, TweetGroup $group)
    {
        $existedTweets = array();
        foreach ($group->tweets as $t)
        {
            $existedTweets[] = $t->text;
        }

        $tweets = array();
        $file = file($fileName);
        $file = array_unique($file);
        while (($tweetText = current($file)) != false)
        {
            next($file);
            $tweetText = trim($tweetText);
            if (empty($tweetText) || $tweetText == '' || in_array($tweetText, $existedTweets)) continue;
            $tweet = new Tweet();
            $tweet->tweet_group_id = $group->id;
            $tweet->times = $group->lessPostedTweetTimes;
            $tweet->text = $tweetText;
            if (!$tweet->save()) {
                throw new Exception(print_r($tweet->getErrors(), true));
            }
            $tweets[] = $tweet;
        }

        return $tweets;
    }

    public static function clearWithoutGroup()
    {
        Yii::app()->db->createCommand('delete tweet from tweet
        left join tweet_group_has_tweet gt on gt.tweet_id = id
        where gt.tweet_id is NULL');
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
        $criteria->compare('text', $this->text, true);
        $criteria->compare('times', $this->times);
        $criteria->compare('is_active', $this->is_active);
        $criteria->compare('created', $this->created, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
            'sort' => array(
                'defaultOrder' => 'created DESC',
            ),
        ));
    }
} 