<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "album".
 *
 * @property integer $id
 * @property string $name
 * @property string $tags
 *
 * @property Media[] $media
 */
class Album extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = "create";
    const SCENARIO_UPDATE = "update";

    public $files;
    
    public static function tableName()
    {
        return 'album';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['tags'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique',
//                'filter' => function ($query) {
//                    $query->where(['not', ['id' => $this->id]]);
//                }, 
                'message' => '{attribute} {value} มีอยู่แล้วในระบบ'
            ],
            [['name'], 'unique',
                'filter' => function ($query) {
                    $query->where(['not', ['id' => $this->id]]);
                }, 
                'message' => '{attribute} {value} มีอยู่แล้วในระบบ',
                'on' => 'update',
            ],
            [['files'], 'file', 'extensions'=>'png, jpg, jpeg, gif, doc, docx, ppt, pptx, txt, pdf, mpeg, mpg, mp4, avi, mov, mp3, wav, ogg ', 'maxFiles' => 10],
            [['files'], 'required', 'on' => 'create'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'ชื่ออัลบั้ม',
            'tags' => 'Tags',
            'files'=>'ไฟล์',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasMany(Media::className(), ['album_id' => 'id']);
    }
    
    public function getIdByName($name=""){
        if($name != "")
            $this->name = $name;
        if($this->id == null)
            $found = Album::find()->where(['name'=>$this->name])->one();
        if($found){
            $this->id = $found->id;
            return $found->id;
        }
        else {
            throw new \Exception('Album name is not exist.');
            return false;
        }
        
    }
}
