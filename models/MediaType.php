<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "media_type".
 *
 * @property integer $id
 * @property string $name
 * @property string $extension
 *
 * @property Media[] $media
 */
class MediaType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'media_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 45, 'filter', 'filter'=>'strtolower'],
            [['extension'], 'string', 'max' => 255, 'filter', 'filter'=>'strtolower'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'ชื่อประเภทสื่อ',
            'extension' => 'นามสกุลไฟล์',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasMany(Media::className(), ['media_type_id' => 'id']);
    }
    
    public static function getExtensionAsString($typeName=['video']){
        $extension_arr = self::find()->select("extension")->where(['in', 'name',$typeName])->asArray()->all();
        $result = "";
        foreach($extension_arr as $arr){
            $result .= ' '.$arr['extension'];
        }
//        $result = str_replace(",", "", $result);
//        $result = str_replace(".", "", $result);
        return $result;
    }
}
