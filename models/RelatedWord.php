<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "related_word".
 *
 * @property integer $word_id1
 * @property integer $word_id2
 * @property integer $priority
 *
 * @property Dictionary $wordId1
 * @property Dictionary $wordId2
 */
class RelatedWord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'related_word';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['word_id1', 'word_id2'], 'required'],
            [['word_id1', 'word_id2', 'priority'], 'integer'],
            [['word_id1'], 'exist', 'skipOnError' => true, 'targetClass' => Dictionary::className(), 'targetAttribute' => ['word_id1' => 'id']],
            [['word_id2'], 'exist', 'skipOnError' => true, 'targetClass' => Dictionary::className(), 'targetAttribute' => ['word_id2' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'word_id1' => 'คำที่ 1',
            'word_id2' => 'คำที่ 2
ต้องไม่อยู่ในคำที่ 1',
            'priority' => 'ระดับความแม่นยำ
0:มีความหมายคือกัน
1~99: มีความหมายใกล้เคียงกัน',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWordId1()
    {
        return $this->hasOne(Dictionary::className(), ['id' => 'word_id1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWordId2()
    {
        return $this->hasOne(Dictionary::className(), ['id' => 'word_id2']);
    }
}
