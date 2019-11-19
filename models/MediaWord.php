<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "media_word".
 *
 * @property integer $id
 * @property string $word
 */
class MediaWord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'media_word';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['word'], 'required'],
            [['word'], 'string', 'max' => 255],
            [['word'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'word' => 'Word',
        ];
    }

    /**
     * @param String $longWord
     */
    public static function createMediaWordByName($longWord){
        $words = Yii::$app->word->split($longWord);
        // Yii::$app->utility->debug($words);
        if(!count($words)){
            return false;
        }
        //$row: columnName => value;
        $rows = [];
        foreach($words as $word){
            $row = [$word];
            array_push($rows, $row);
        }
        // Yii::$app->utility->debug($rows,false);
        $queryBuilder = Yii::$app->db->createCommand()->batchInsert(MediaWord::tableName(), ['word'], $rows)->getRawSql();
        $queryBuilder = Yii::$app->db->createCommand(str_replace("INSERT", "INSERT IGNORE", $queryBuilder));
        try{
            $queryBuilder->execute();
            return true;
        }catch(\Exception $e){
            echo $e->getMessage();
            return false;
        }
    }
    
    /**
     * @param String $strTags
     */
    public static function createMediaWordByTags($strTags){
        if(count($strTags) == 0) return true;
        
        $tags = explode(",", $strTags);
        $rows = [];
        foreach($tags as $tag){
            $row = [$tag];
            array_push($rows, $row);
        }
        $queryBuilder = Yii::$app->db->createCommand()->batchInsert(MediaWord::tableName(), ['word'], $rows)->getRawSql();
        $queryBuilder = Yii::$app->db->createCommand(str_replace("INSERT", "INSERT IGNORE", $queryBuilder));
        try{
            $queryBuilder->execute();
            return true;
        }catch(\Exception $e){
            print_r($e);
            print_r($tags);
            return false;
        }
    }

    /**
     * isMediaWord
     * Use to Check $word that related to media
     * @param mixed $word 
     * @return boolean return true if $word related to media else return false
     */
    public static function isMediaWord($word){
        $condition = ['or'];
        if(is_array($word)){
            foreach($word as $w){
                array_push($condition, ['LIKE', 'word', $w]);
            }
        }else{
            $condition = ['LIKE', 'word', $word];
        }
        return MediaWord::find()->where($condition)->exists();
    }
}
