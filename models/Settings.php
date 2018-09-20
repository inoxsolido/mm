<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property integer $id
 * @property integer $frequency_word_rate
 * @property integer $frequency_relation_rate
 * @property string $ftp_host
 * @property string $ftp_user
 * @property string $ftp_password
 * @property string $ftp_part
 * @property string $http_part
 */
class Settings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'frequency_word_rate', 'frequency_relation_rate'], 'required'],
            [['id', 'frequency_word_rate', 'frequency_relation_rate'], 'integer'],
            [['ftp_host', 'ftp_user', 'ftp_password', 'ftp_part', 'http_part'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'frequency_word_rate' => 'ความถี่ของคำค้นหาบ่อยที่จะนำไปใช้ตัดคำ',
            'frequency_relation_rate' => 'ความถี่ของคำที่มีความเกี่ยวข้องกันที่จะนำไปใช้ค้นหา',
            'ftp_host' => 'IP ของ FTP',
            'ftp_user' => 'ชื่อเข้าใช้งาน FTP',
            'ftp_password' => 'รหัสเข้าใช้งาน FTP',
            'ftp_part' => 'ตำแหน่งเริ่มต้นในการเก็บไฟล์',
            'http_part'=> 'ตำแหน่งเริ่มต้นในการเรียกใช้ไฟล์ผ่าน Http'
        ];
    }
    
    
    public function beforeSave($insert){
        $old_model = Settings::find()->one();
        if($this->ftp_password !== $old_model->ftp_password)
            $this->ftp_password = Yii::$app->encryption->encryptFtpPassword($this->ftp_password);
        return parent::beforeSave($insert);
    }
    
    public function getRealFtpPassword(){
        return Yii::$app->encryption->decryptFtpPassword($this->ftp_password);
    }
    
    public function getModel(){
        $model = $this->find()->one();
        if($model)
            return $model;
        else{
            throw new \yii\web\ServerErrorHttpException("The server was not set up.");
            return NULL;
        }
    }
    
    static function getSetting(){
        $model = self::find()->one();
        if($model)
            return $model;
        else{
            throw new \yii\web\ServerErrorHttpException("The server was not set up.");
            return NULL;
        }
    }
}
