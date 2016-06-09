<?php

/**
 * This is the model class for table "twitter_log".
 *
 * The followings are the available columns in table 'twitter_log':
 * @property integer $id
 * @property integer $twitter_account_id
 * @property string $method
 * @property integer $http_code
 * @property string $response
 * @property string $created
 *
 * The followings are the available model relations:
 * @property TwitterAccount $twitterAccount
 */
class TwitterLog extends BaseActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @return TwitterLog the static model class
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
        return 'twitter_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('method, response, created', 'required'),
            array('id', 'numerical', 'integerOnly'=>true),
            array('method', 'length', 'max'=>45),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, method, response, created', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'twitter_account_id' => 'Twitter Account',
            'method' => 'Method',
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

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('method',$this->method,true);
        $criteria->compare('response',$this->response,true);
        $criteria->compare('created',$this->created,true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
        ));
    }
} 