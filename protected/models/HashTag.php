<?php
/*
 * update hash_tag ht, hash_tag_group_has_hash_tag htt
 set ht.hash_tag_group_id = htt.hash_tag_group_id
 where ht.id = htt.hash_tag_id
*/
/**
 * This is the model class for table "hash_tag".
 *
 * The followings are the available columns in table 'hash_tag':
 * @property integer $id
 * @property integer $hash_tag_group_id
 * @property string $text
 * @property string $created
 * @property integer $is_active
 *
 * The followings are the available model relations:
 * @property HashTagGroup[] $hashTagGroups
 */
class HashTag extends BaseActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return HashTag the static model class
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
		return 'hash_tag';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('text, created, hash_tag_group_id', 'required'),
			array('id, is_active, hash_tag_group_id', 'numerical', 'integerOnly'=>true),
			array('text', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, text, created, is_active', 'safe', 'on'=>'search'),
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
			'hashTagGroup' => array(self::BELONGS_TO, 'HashTagGroup', 'hash_tag_group_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'text' => 'Text',
			'created' => 'Created',
			'is_active' => 'Is Active',
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
		$criteria->compare('text',$this->text,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('is_active',$this->is_active);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}