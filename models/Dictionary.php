<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dictionary".
 *
 * @property integer $id
 * @property string $word
 * @property integer $length
 */
class Dictionary extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dictionary';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['word'], 'required'],
            [['length'], 'integer'],
            [['word'], 'string', 'max' => 60, 'on'=>'single'],
            [['word'], 'unique', 'message' => '{attribute} {value} มีอยู่แล้วในระบบ']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'word' => 'คำศัพท์',
            'length' => 'จำนวนตัวอักษร',
        ];
    }
    
    public function beforeSave($insert){
        $this->length = mb_strlen($this->word, "UTF-8");
        return parent::beforeSave($insert);
    }
}
