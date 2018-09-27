<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "frequency_word".
 *
 * @property integer $id
 * @property string $word
 * @property integer $frequency
 */
class FrequencyWord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'frequency_word';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['word'], 'required'],
            [['word'], 'string'],
            [['word'], 'unique'],
            [['frequency'], 'integer'],
            [['frequency'], 'default', 'value'=>1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'word' => 'คำค้นหา',
            'frequency' => 'ความถี่',
        ];
    }
}
