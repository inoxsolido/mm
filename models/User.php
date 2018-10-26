<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $name
 * @property string $surname
 * @property string $image_path 
 * @property integer $user_type_id
 * 
 *
 * @property UserType $userType
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{

    public $image_file;
    public $password_confirm;
    public $password_old;

    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'name', 'surname', 'user_type_id'], 'required'],
            [['user_type_id'], 'integer'],
            [['username', 'password', 'password_confirm', 'name', 'surname'], 'string', 'max' => 45],
            [['email', 'image_path'], 'string', 'max' => 255],
            [['username'], 'unique'],
            [['password_confirm', 'password'], 'required', 'on' => 'create'],
            [['password_old'], 'required', 'on' => 'personal'],
            [['password_old'], 'validateOldPassword', 'on' => 'personal'],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => "Passwords don't match" ],
            [['user_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserType::className(), 'targetAttribute' => ['user_type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'ชื่อสำหรับลงชื่อเข้าใช้งาน',
            'password' => 'รหัสผ่านสำหรับเข้าระบบ',
            'password_confirm' => 'รหัสผ่านอีกครั้ง',
            'email' => 'Email ของผู้ใช้งาน',
            'name' => 'ชื่อจริงผู้ใช้งาน',
            'surname' => 'นามสกุลผู้ใช้งาน',
            'image_file' => 'รูปภาพผู้ใช้งาน',
            'image_path' => 'ตำแหน่งไฟล์ภาพ',
            'user_type_id' => 'ประเภทผู้ใช้งาน',
            'type'=>'ประเภทผู้ใช้งาน',
            'password_old'=>'รหัสผ่านปัจจุบัน',
        ];
    }

    public function getImage(){
        if($this->image_path){
            return \yii\helpers\Html::img(Yii::$app->request->absoluteUrl.'/'.$this->image_path, ['class'=>'user-image']);
        }else{
            return "";
        }
    }
    public function getImageCircle(){
        if($this->image_path){
            return \yii\helpers\Html::img(Yii::$app->request->absoluteUrl.'/'.$this->image_path, ['class'=>'img-circle']);
        }else{
            return "";
        }
    }
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user)
        {
            if ($user['accessToken'] === $token)
            {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()->where(['username' => $username])->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
//        return $this->authKey === $authKey;
        return TRUE;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === Yii::$app->encryption->encryptUserPassword($password);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserType()
    {
        return $this->hasOne(UserType::className(), ['id' => 'user_type_id']);
    }
    /* 
     * check this user is admin
     * this function can call from Yii::$app->user->identity->getIsAdmin();
     * 
     * return boolean
     * 
     */
    public function getIsAdmin(){
        return @$this->user_type_id === 1;
    }

    public function validateOldPassword($attribute, $params)
    {
        if (Yii::$app->encryption->encryptUserPassword($this->password_old) !== $this->getOldAttribute('password')) {
            $this->addError($attribute, 'Incorrect password.');
        }
    }
}
