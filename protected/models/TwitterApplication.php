<?php

/**
 * This is the model class for table "twitter_application".
 *
 * The followings are the available columns in table 'twitter_application':
 * @property integer $id
 * @property string $name
 * @property string $consumer_key
 * @property string $consumer_secret
 * @property string $proxy
 *
 * The followings are the available model relations:
 * @property PostedVariant[] $postedTweets
 */
class TwitterApplication extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @return TwitterApplication the static model class
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
        return 'twitter_application';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, consumer_key, consumer_secret', 'required'),
            array('name', 'length', 'max' => 45),
            array('consumer_key, consumer_secret, proxy', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, consumer_key, consumer_secret, proxy', 'safe', 'on' => 'search'),
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
            'postedTweets' => array(self::HAS_MANY, 'PostedVariant', 'twitter_application_id'),
        );
    }

    /**
     * @return TwitterApplication.
     */
    public static function getDefault()
    {
        return TwitterApplication::model()->find();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'consumer_key' => 'Consumer Key',
            'consumer_secret' => 'Consumer Secret',
            'proxy' => 'Proxy',
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
        $criteria->compare('consumer_key', $this->consumer_key, true);
        $criteria->compare('consumer_secret', $this->consumer_secret, true);
        $criteria->compare('proxy', $this->proxy, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }
} 