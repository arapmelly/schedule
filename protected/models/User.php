<?php
/**
 * Created by JetBrains PhpStorm.
 * User: andrew
 * Date: 3/27/11
 * Time: 8:39 PM
 * To change this template use File | Settings | File Templates.
 */
/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property integer superuser
 *
 * The followings are the available model relations:
 * @property Tweet[] $tweets
 * @property TweetGroup[] $groups
 * @property HashTagGroup[] $hashTagGroups
 * @property TwitterAccount[] $twitterAccounts
 * @property TwitterAccount[] $activeTwitterAccounts
 */
class User extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function primaryKey()
    {
        return 'id';
    }

    public function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('superuser', 'default', 'value' => 0),
            array('username, password', 'required'),
            array('id', 'numerical', 'integerOnly' => true),
            array('username, email, password', 'length', 'max' => 45),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, username, email, password', 'safe', 'on' => 'search'),
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
            'groups' => array(self::HAS_MANY, 'TweetGroup', 'user_id'),
            'tweets' => array(self::HAS_MANY, 'Tweet', 'user_id'),
            'twitterAccounts' => array(self::HAS_MANY, 'TwitterAccount', 'user_id'),
            'activeTwitterAccounts' => array(self::HAS_MANY, 'TwitterAccount', 'user_id', 'condition' => 'is_active = 1 and disabled_until < UTC_TIMESTAMP()'),
            'hashTagGroups' => array(self::HAS_MANY, 'HashTagGroup', 'user_id'),
        );
    }

    public function isAdmin()
    {
        return $this->superuser == 1;
    }

    public function init()
    {
        $this->onPostVariant = array(new PostedVariant(), 'handleNewPost');
    }

    private $_twitterApiArray = array();

    /**
     * @return TwitterApi the static model class
     */
    public function getTwitterApiForAccount(TwitterAccount $twitterAccount)
    {
        if (!isset($this->_twitterApiArray[$twitterAccount->id])) {
            $this->_twitterApiArray[$twitterAccount->id] = new TwitterApi(TwitterApplication::getDefault(), $twitterAccount);
        }
        return $this->_twitterApiArray[$twitterAccount->id];
    }

    private static $_current_user;

    /**
     * @return User
     */
    public static function current()
    {
        if (is_null(self::$_current_user)) {
            if (Yii::app()->user)
                self::$_current_user = User::model()->findByPk(Yii::app()->user->id);
            else
                self::$_current_user = false;
        }
        return self::$_current_user;
    }

    public function post(TwitterAccount $twitterAccount, $variants)
    {
        if ($variants instanceof TweetVariantGroup)
            return $this->postGroup($twitterAccount, $variants);
        elseif ($variants instanceof TweetVariant)
            return $this->postVariant($twitterAccount, $variants);
        return false;
    }

    public function postVariant(TwitterAccount $twitterAccount, TweetVariant $variant)
    {
        $api = $this->getTwitterApiForAccount($twitterAccount);
        if (($status = $api->statusesUpdate(Tweet::crop($variant->text))) == true) {
            $twitterAccount->randomlyDisable();
        }
        $variant->incCounter();
        $this->onPostVariant(new Event($this, array(
            'user' => $this,
            'application' => $api->getApplication(),
            'account' => $twitterAccount,
            'variant' => $variant,
            'http_code' => $api->getLastCode(),
            'response' => $api->getLastResponse()
        )));
        sleep(rand(1, 3));

        return $status;
    }

    public function postGroup(TwitterAccount $twitterAccount, TweetVariantGroup $variantGroup)
    {
        $api = $this->getTwitterApiForAccount($twitterAccount);
        $postedCount = 0;
        foreach ($variantGroup->tweetVariants as $variant)
        {
            if (($status = $api->statusesUpdate(Tweet::crop($variant->text))) == true) {
                $postedCount++;
            }
            $this->onPostVariant(new Event($this, array(
                'user' => $this,
                'application' => $api->getApplication(),
                'account' => $twitterAccount,
                'tweetVariantGroup' => $variantGroup,
                'variant' => $variant,
                'http_code' => $api->getLastCode(),
                'response' => $api->getLastResponse()
            )));
            $variant->incCounter();
            sleep(rand(1, 3));
        }
        if ($postedCount > 0)
            $twitterAccount->randomlyDisable($postedCount);

        return $postedCount;
    }

    public function postText(TwitterAccount $twitterAccount, $text)
    {
        $api = $this->getTwitterApiForAccount($twitterAccount);
        if (($status = $api->statusesUpdate(Tweet::crop($text))) == true) {

        }
        return $status;
    }

    public function onPostVariant($event)
    {
        $this->raiseEvent('onPostVariant', $event);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'username' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
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
        $criteria->compare('username', $this->username, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('password', $this->password, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
        ));
    }

}

class Event extends CEvent
{
    public $params;

    public function __construct($sender = null, $params = null)
    {
        parent::__construct($sender);
        $this->params = $params;
    }
}
