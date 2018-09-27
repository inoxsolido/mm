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
            [['word1', 'word2'], 'required'],
            [['word1', 'word2'], 'string'],
            [['word1', 'word2'], 'doubleUnique'],
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
            'word1' => 'คำค้นแรก',
            'word2' => 'คำค้นถัดไป',
            'frequency' => 'ความถี่',
        ];
    }
    
    public function doubleUnique($attribute, $params, $validator){
        if(self::find()
                ->orWhere(['and',['word1'=>$this->word1], ['word2'=>$this->word2]])
                ->orWhere(['and',['word1'=>$this->word2], ['word2'=>$this->word1]])
                ->andWhere(['not',['id'=>$this->id]])->exists()){
            $this->addError('word1',"$this->word1 and $this->word2 is exist!");
            $this->addError('word2',"$this->word1 and $this->word2 is exist!");
            return false;
        }
        return true;
    }
}
