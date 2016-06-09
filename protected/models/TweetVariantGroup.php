<?php

/**
 * This is the model class for table "variant_group".
 *
 * The followings are the available columns in table 'variant_group':
 * @property integer $id
 * @property integer $tweet_id
 * @property integer $hash_tag_group_id
 *
 * The followings are the available model relations:
 * @property PostedVariant[] $postedVariants
 * @property TweetVariant[] $tweetVariants
 */
class TweetVariantGroup extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return TweetVariantGroup the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tweet_variant_group';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tweet_id, hash_tag_group_id', 'required'),
			array('tweet_id, hash_tag_group_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, tweet_id, hash_tag_group_id', 'safe', 'on'=>'search'),
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
			'postedVariants' => array(self::HAS_MANY, 'PostedVariant', 'tweet_variant_group_id'),
			'tweetVariants' => array(self::HAS_MANY, 'TweetVariant', 'tweet_variant_group_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'tweet_id' => 'Tweet',
			'hash_tag_group_id' => 'Hash Tag Group',
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('tweet_id',$this->tweet_id);
		$criteria->compare('hash_tag_group_id',$this->hash_tag_group_id);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}