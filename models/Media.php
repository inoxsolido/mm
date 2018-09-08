<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "media".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $file_name
 * @property string $file_extension
 * @property string $file_path
 * @property string $file_upload_date
 * @property string $file_thumbnail_path
 * @property string $tags
 * @property integer $is_public
 * @property integer $media_type_id
 * @property integer $album_id
 *
 * @property Album $album
 * @property MediaType $mediaType
 *
 * @property file $media_file
 * @property file $thumbnail_file
 * @property string $thumbnail_from_video
 */
class Media extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    const SCENARIO_CREATE = "create";
    const SCENARIO_VIDEO = "video";
    const SCENARIO_OTHER = "other";

    public $media_file;
    public $thumbnail_file;
    public $thumbnail_from_video;
    public static function tableName()
    {
        return 'media';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'file_name', 'file_extension', 'file_path', 'file_upload_date', 'file_thumbnail_path', 'media_type_id'], 'required'],
            [['id', 'is_public', 'media_type_id', 'album_id'], 'integer'],
            [['file_path', 'tags'], 'string'],
            [['file_upload_date', 'thumbnail_from_video'], 'safe'],
            [['name', 'file_name'], 'string', 'max' => 255],
            [['file_extension'], 'string', 'max' => 10],
            [['album_id'], 'exist', 'skipOnError' => true, 'targetClass' => Album::className(), 'targetAttribute' => ['album_id' => 'id']],
            [['media_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => MediaType::className(), 'targetAttribute' => ['media_type_id' => 'id']],
            [['thumbnail_file'], 'file', 'extensions'=> (new MediaType())->getExtensionAsString('image')],
            [['media_file'], 'file', 'extensions'=>(new MediaType())->getExtensionAsString('video'), 'on'=>'video'],
            [['media_file'], \app\components\MyFileValidator::className(), 'extensions'=>(new MediaType())->getExtensionAsString(['video', 'image']), 'on'=>'other'],
            [['media_file'], 'required', 'on'=>['create','video','other']],
//            [['thumbnail_file'], 'required', 'when' => function(){
//                if($this->thumbnail_from_video == '') return true;
//                else return false;
//            },],
            [['thumbnail_file'], \app\components\RequiredWhenOneEmptyValidator::className(), 'emptyAttribute'=>'thumbnail_from_video', 'on'=>'video'],
            [['thumbnail_file'], 'required', 'on'=>'other'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'ชื่อสื่อ',
//            'description' => 'รายละเอียดสื่อ',
            'file_name' => 'ชื่อไฟล์ที่เก็บใน Storage',
            'file_extension' => 'File Extension',
            'file_path' => 'ตำแหน่งไฟล์ใน Storage',
            'file_upload_date' => 'File Upload Date',
            'file_thumbnail_path' => 'ภาพตัวอย่าง',
            'tags' => 'Keyword สำคัญที่ใช้ค้นหา',
            'is_public' => 'Guest สามารถเข้าถึงได้',
            'media_type_id' => 'ประเภทไฟล์',
            'album_id' => 'อัลบั้ม',
            'media_file' => 'เลือกไฟล์',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlbum()
    {
        return $this->hasOne(Album::className(), ['id' => 'album_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMediaType()
    {
        return $this->hasOne(MediaType::className(), ['id' => 'media_type_id']);
    }

    public function updateFileDate(){
        $date = date("Y-m-d H:i:s");
        $this->file_upload_date = $date;
        return date("Y-m-d-H-i-s",  strtotime($date));
    }

    public function getHttpPath($setting=''){
        if(!$setting) $setting = Settings::findOne(1);
        return 'http://'.$setting->ftp_host.$setting->http_part.'/'.$this->file_path.'/'.$this->file_name.$this->file_extension;
    }

    public function getFtpPath($setting=''){
        if(!$setting) $setting = Settings::findOne(1);
        return $setting->ftp_part.$this->file_path.'/'.$this->file_name.$this->file_extension;
    }


    public function getNewFileName($is_new_record = true, $file_path=""){
        if($file_path) $this->file_path = $file_path;
        $new_file_name = $this->name;
        $last_record = $is_new_record?
            Media::find()
                ->select(["file_path", "file_upload_date", "file_name","name", "id"])
                ->where(["file_path" => $this->file_path])
                ->andWhere(['name'=>$new_file_name])
                ->orderBy(["file_upload_date"=>SORT_DESC])
                ->one()
            :Media::find()
                ->select(["file_path", "file_upload_date", "file_name","name", "id"])
                ->where(['!=', 'id' ,$this->id])
                ->andwhere(["file_path" => $this->file_path])
                ->andWhere(['name'=>$new_file_name])
                ->orderBy(["file_upload_date"=>SORT_DESC])
                ->one();
        $last_number = 0;
//                print_r($last_record); die();
        if($last_record){
            //GET NUMBER FROM FILE_NAME
            $file_name = $last_record->file_name;
            $last_number = preg_match('/\((?<digit>\d+)\)$/',$file_name,$matches);//IF DOSEN'T MATCH, RETURN 0

            if($last_number) {//IF MATCHED, RETURN LAST_NUMBER
                $last_number = $matches['digit']+1;
                $last_number = "($last_number)";
                $new_file_name = preg_replace('/\((\d+)\)$/',  $last_number, $file_name);
            }
            else{//CREATE (\d) PATTERN IN FILE_NAME
                $new_file_name .= "(2)";
            }
            return $new_file_name;
        }
        return $this->name;

    }
    public function getThumbnailDecoded(){
        if($this->thumbnail_from_video){
            $data = $this->thumbnail_from_video;
            list($type, $data) = explode(';', $data);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);

//            list($type, $this->$thumbnail_from_video) = explode(';', $this->thumbnail_from_video);
//            list(, $this->$thumbnail_from_video) = explode(',', $this->thumbnail_from_video);
//            $this->thumbnail_from_video = base64_decode($this->thumbnail_from_video);
            $this->thumbnail_from_video = $data;
            return $data;
        }else{
            return null;
        }
    }

    public function getThumbnailHttpPath($setting=''){
        if(!$setting) $setting = Settings::findOne(1);
        return 'http://'.$setting->ftp_host.$setting->http_part.'/'.$this->file_thumbnail_path;
    }

    public function getThumbnailFtpPath($setting=''){
        if(!$setting) $setting = Settings::findOne(1);
        return $setting->ftp_part.$this->file_thumbnail_path;
    }


}
