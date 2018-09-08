<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "frequency_relation".
 *
 * @property integer $id
 * @property string $word1
 * @property string $word2
 * @property integer $frequency
 */
class FrequencyRelation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'frequency_relation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['word1', 'word2', 'frequency'], 'required'],
            [['word1', 'word2'], 'string'],
            [['frequency'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'word1' => 'คำค้นแรก',
            'word2' => 'คำค้นถัดไป',
            'frequency' => 'ความถี่',
        ];
    }
}
