<?php

/**
 * This is the model class for table "tweet_variant".
 *
 * The followings are the available columns in table 'tweet_variant':
 * @property integer $id
 * @property integer $tweet_id
 * @property string $text
 * @property integer $tweet_variant_group_id
 *
 * The followings are the available model relations:
 * @property PostedVariant[] $postedVariants
 * @property TweetVariantGroup $variantGroup
 */
class TweetVariant extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @return TweetVariant the static model class
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
        return 'tweet_variant';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('tweet_id, text', 'required'),
            array('tweet_id, tweet_variant_group_id', 'numerical', 'integerOnly' => true),
            array('text', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, tweet_id, text, tweet_variant_group_id', 'safe', 'on' => 'search'),
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
            'postedVariants' => array(self::HAS_MANY, 'PostedVariant', 'tweet_variant_id'),
            'variantGroup' => array(self::BELONGS_TO, 'TweetVariantGroup', 'tweet_variant_group_id'),
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
            'text' => 'Text',
        );
    }

    public function incCounter()
    {
        $res = Yii::app()->db->createCommand('UPDATE `tweet` SET `times`= times + 1 WHERE id = ' . $this->tweet_id)->execute();
        if ($res == 0)
            throw new Exception();
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

        $criteria->compare('id', $this->id, true);
        $criteria->compare('tweet_id', $this->tweet_id);
        $criteria->compare('text', $this->text, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
} 