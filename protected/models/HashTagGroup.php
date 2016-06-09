<?php

/**
 * This is the model class for table "hash_tag_group".
 *
 * The followings are the available columns in table 'hash_tag_group':
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property integer $is_active
 * @property string $created
 *
 * The followings are the available model relations:
 * @property User $user
 * @property HashTag[] $hashTags
 * @property TweetGroup[] $tweetGroups
 */
class HashTagGroup extends BaseActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @return HashTagGroup the static model class
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
        return 'hash_tag_group';
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
            array('id, user_id, is_active', 'numerical', 'integerOnly' => true),
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
            'hashTags' => array(self::HAS_MANY, 'HashTag', 'hash_tag_group_id'),
            'tweetGroups' => array(self::MANY_MANY, 'TweetGroup', 'tweet_group_use_hash_tag_group(hash_tag_group_id, tweet_group_id)'),
        );
    }

    protected function beforeDelete()
    {
        Yii::app()->db->createCommand()
            ->delete('tweet_group_use_hash_tag_group', 'hash_tag_group_id = :hash_tag_group_id', array(':hash_tag_group_id' => $this->id));

        $tags = $this->hashTags;
        $transaction = Yii::app()->db->beginTransaction();
        foreach ($tags as $tag)
        {
            $tag->delete();
        }
        $transaction->commit();
        return parent::beforeDelete();
    }

    public function getHashTags()
    {
        $tags = array();
        foreach ($this->hashTags as $ht)
        {
            $tags[] = $ht->text;
        }
        return $tags;
    }

//    public function hasTweet($tweetId = null, $text = null)
//    {
//        $criteria = new CDbCriteria();
//        $criteria->join = 'JOIN tweet_group_has_tweet ON tweet_id = t.id';
//        $criteria->compare('tweet_group_id', $this->id);
//        if (!empty($tweetId)) {
//            $criteria->compare('t.id', $tweetId);
//        }
//        if (!empty($text)) {
//            $criteria->compare('t.text', $text);
//        }
//        if (Tweet::model()->find($criteria)) {
//            return true;
//        }
//        return false;
//    }

    public function createHashTag($hashTagText)
    {
        $hashTagText = trim($hashTagText);
        if (HashTag::model()->findByAttributes(array('text' => $hashTagText, 'hash_tag_group_id' => $this->id))) {
            return false;
        }
        $hashTag = new HashTag();
        $hashTag->hash_tag_group_id = $this->id;
        $hashTag->text = $hashTagText;
        $hashTag->save();

        return $hashTag;
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

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
}