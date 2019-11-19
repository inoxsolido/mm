<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dictionary".
 *
 * @property integer $id
 * @property string $word
 * @property integer $length
 * @property boolean $media_tag
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

    /**
     * clearNonExistMediaWord
     * Use to clear non-exist media_word
     * @throw DbException
     * @return boolean
     */
    public static function clearNonExistMediaWord(){
        //open transaction
        //clear all flag to 0
        //get all media and album name, tag
        //split them
        //change all flag those in media, album's name and tag
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $media_name = Media::find()->select(['name']);
            $album_name = Album::find()->select(['name']);
            $names = $media_name->union($album_name)->asArray()->all();//2 dimensions array
            $media_tags = Media::find()->select(['tags']);
            $album_tags = Media::find()->select(['tags']);
            
            //convert results to 1 dimension array
            $result_1 = [];
            $result_2 = [];
            foreach ($names as $key => $value) {
                
                $splitted = Yii::$app->word->split($value['name']);
                if($splitted) $result_1 = array_merge($result_1, $splitted);
                unset($splitted);//memory optimization
                unset($names[$key]);//memory optimization
            }
            
            $tags = $media_tags->union($album_tags)->asArray()->all();// 2 dimensions array
            foreach ($tags as $key => $value) {
                $splitted = explode(",",$value['tags']);
                if(count($splitted)) array_merge($result_2, $splitted);
            }
            $result = array_merge($result_1, $result_2);
            unset($result_1);//memory optimization
            unset($result_2);//memory optimization
            if(!count($result)){//ถ้าไม่มีในไฟล์สื่อทั้งหมด ไม่มีคำ(ไม่มีชื่อไม่มีtag)เลย
                MediaWord::deleteAll();//ให้ลบ media_word ทิ้งทั้งหมด
            }else{
                // $media_words = MediaWord::find()->all();
                // foreach ($media_words as $mword){
                //     if(!in_array($mword->word, $result)){
                //         $mword->delete();
                //     }
                // }
                //transform to delete from media_word where word in($result);//$result ant,bird,cat,dog
                MediaWord::deleteAll(['not in', 'word', $result]);
            }
            
            
            $transaction->commit();
            return true;
        }catch(\Exception $e){
            $transaction->rollback();
            throw $e;
        }
    }

}
