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
    const SCENARIO_UPDATE = "update";
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
        
        
        $result = [
            [['name', 'file_name', 'file_extension', 'file_path', 'file_upload_date', 'file_thumbnail_path', 'media_type_id'], 'required'],
            [['id', 'is_public', 'media_type_id', 'album_id'], 'integer'],
            [['file_path', 'tags'], 'string'],
            [['file_upload_date', 'thumbnail_from_video'], 'safe'],
            [['name', 'file_name'], 'string', 'max' => 255],
            [['file_extension'], 'string', 'max' => 10],
            [['album_id'], 'exist', 'skipOnError' => true, 'targetClass' => Album::className(), 'targetAttribute' => ['album_id' => 'id']],
            [['media_type_id'], 'exist', 'skipOnError' => false, 'targetClass' => MediaType::className(), 'targetAttribute' => ['media_type_id' => 'id']],
            
            [['media_file'], 'required', 'on'=>['create','video','other']],
            [['thumbnail_file'], \app\components\RequiredWhenOneEmptyValidator::className(), 'emptyAttribute'=>'thumbnail_from_video', 'on'=>'video'],
            [['thumbnail_file'], 'required', 'on'=>'other'],
            
        ];
        //optimizer lowest query
        if($this->scenario === self::SCENARIO_CREATE){
            $result[] = [['thumbnail_file'], 'file', 'extensions'=> MediaType::getExtensionAsString('image', false)];
        }else if($this->scenario === self::SCENARIO_UPDATE){
            $result[] = [['thumbnail_file'], 'file', 'extensions'=> MediaType::getExtensionAsString('image', false)];
        }else if($this->scenario === self::SCENARIO_VIDEO){
            $result[] = [['media_file'], 'file', 'extensions'=>MediaType::getExtensionAsString('video', false), 'on'=>'video'];
        }else if($this->scenario === self::SCENARIO_OTHER){
            $result[] = [['media_file'], \app\components\FileExtensionNotInValidator::className(), 'extensions'=>MediaType::getExtensionAsString(['video', 'image'], false), 'on'=>'other'];
        }
        return $result;
    }
//    public function scenarios() {
//        $scenarios = parent::scenarios();
//        $scenarios[self::SCENARIO_CREATE] = [['thumbnail_file'], 'file', 'extensions'=> MediaType::getExtensionAsString('image')];
//        $scenarios[self::SCENARIO_UPDATE] = [['thumbnail_file'], 'file', 'extensions'=> MediaType::getExtensionAsString('image')];
//        $scenarios[self::SCENARIO_VIDEO] = [['media_file'], 'file', 'extensions'=>MediaType::getExtensionAsString('video'), 'on'=>'video'];
//        $scenarios[self::SCENARIO_OTHER] = [$scenarios['default'],[['media_file'], \app\components\FileExtensionNotInValidator::className(), 'extensions'=>MediaType::getExtensionAsString(['video', 'image']), 'on'=>'other']];
//        return $scenarios;
//    }
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
            'file_upload_date' => 'วันที่อัพโหลดไฟล์',
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
        if(!$setting) $setting = Settings::getSetting();
        return 'http://'.$setting->ftp_host.$setting->http_part.'/'.$this->file_path.'/'.$this->file_name.$this->file_extension;
    }

    public function getFtpPath($setting=''){
        if(!$setting) $setting = Settings::getSetting();
        return $setting->ftp_part.$this->file_path.'/'.$this->file_name.$this->file_extension;
    }
    
    /**
     * Get new file name with no exist
     * @param type $is_new_record
     * @param type $file_path
     * @return string
     */
    public function getNewFileName($is_new_record = true, $file_path=""){
        if($file_path) $this->file_path = $file_path;
        $new_file_name = str_replace("#","",$this->name);
        $last_record = $is_new_record?
            Media::find()
                ->select(["file_path", "file_upload_date", "file_name","name", "id"])
                ->where(["file_path" => $this->file_path])
                ->andWhere(['file_name'=>$new_file_name])
                ->orderBy(["file_upload_date"=>SORT_DESC])
                ->one()
            :Media::find()
                ->select(["file_path", "file_upload_date", "file_name","name", "id"])
                ->where(['!=', 'id' ,$this->id])
                ->andwhere(["file_path" => $this->file_path])
                ->andWhere(['file_name'=>$new_file_name])
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
        return $new_file_name;

    }
    /**
     * Generate unique thumbnail path
     * @param string $extension
     * @param \app\models\Settings $setting
     * @param \app\components\FtpClient $ftp
     */
    public function generateUniqueThumbnailPath($extension, $setting='', $ftp=''){
        if($setting == '') $setting = Settings::getSetting();
        if($ftp == ''){
            $ftp = new \app\components\FtpClient();
            $ftp->connect($setting->ftp_host);
            $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
            $ftp->pasv(true);
        }
        $thumbnail_path = $setting->ftp_part.'/thumbnails/';
        $new_thumbnail_name = Yii::$app->getSecurity()->generateRandomString();
        $path_name = $thumbnail_path.$new_thumbnail_name;
        
        $file_list = $ftp->nlist($thumbnail_path);
        while(in_array($path_name, $file_list)){
            $new_thumbnail_name = Yii::$app->getSecurity()->generateRandomString();
            $path_name = $thumbnail_path.$new_thumbnail_name;
            $file_list = $ftp->nlist($thumbnail_path);
        }
        $this->file_thumbnail_path = '/thumbnails/'.$new_thumbnail_name.$extension;
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
        if($this->file_thumbnail_path == ''){
            //send path of default image by media type @not complete !!
            return '';
        }
        if(!$setting) $setting = Settings::getSetting();
        return 'http://'.$setting->ftp_host.$setting->http_part.'/'.$this->file_thumbnail_path;
    }

    public function getThumbnailFtpPath($setting=''){
        if(!$setting) $setting = Settings::getSetting();
        return $setting->ftp_part.$this->file_thumbnail_path;
    }
    
    public static function generateThumbnailHttp($path,$setting){
        if(!$setting) $setting = Settings::getSetting();
        return 'http://'.$setting->ftp_host.$setting->http_part.'/'.$path;
    }
    
    public static function generateFileHttp($path, $fileName, $extension, $setting){
        if(!$setting) $setting = Settings::getSetting();
        return 'http://'.$setting->ftp_host.$setting->http_part.'/'.$path.'/'.$fileName.$extension;
    }


    /**
     * Deletes the table row corresponding to this active record.
     *
     * This method performs the following steps in order:
     *
     * 1. call [[beforeDelete()]]. If the method returns `false`, it will skip the
     *    rest of the steps;
     * 2. delete the record from the database;
     * 3. call [[afterDelete()]].
     *
     * In the above step 1 and 3, events named [[EVENT_BEFORE_DELETE]] and [[EVENT_AFTER_DELETE]]
     * will be raised by the corresponding methods.
     *
     * @return int|false the number of rows deleted, or `false` if the deletion is unsuccessful for some reason.
     * Note that it is possible the number of rows deleted is 0, even though the deletion execution is successful.
     * @throws StaleObjectException if [[optimisticLock|optimistic locking]] is enabled and the data
     * being deleted is outdated.
     * @throws \Exception|\Throwable in case delete failed.
     */
    public function delete()
    {
        $setting = Settings::getSetting();
        $ftp = new \app\components\FtpClient();
        $ftp->connect($setting->ftp_host);
        $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
        $ftp->pasv(true);
        
        $file_path = $this->getFtpPath($setting);
        $thumbnail_path = $this->getThumbnailFtpPath($setting);
        
        if (!$this->isTransactional(self::OP_DELETE)) {
            $result = $this->deleteInternal();
            if($result){
                @$ftp->delete($file_path);
                @$ftp->delete($thumbnail_path);
            }
            return $result;
        }
        $transaction = static::getDb()->beginTransaction();
        try {
            $result = $this->deleteInternal();
            if ($result === false) {
                $transaction->rollBack();
            } else {
                @$ftp->delete($file_path);
                @$ftp->delete($thumbnail_path);
                $transaction->commit();
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
    
    public static function deleteAll($condition = null, $params = [], $ftp='')
    {
        $command = static::getDb()->createCommand();
        $command->delete(static::tableName(), $condition, $params);
        
        $setting = Settings::getSetting();
        if(!$ftp){
            $ftp = new \app\components\FtpClient();
            $ftp->connect($setting->ftp_host);
            $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
            $ftp->pasv(true);
        }
        
        //get all media same condition to delete
        $models = static::find()->where($condition, $params)->all();
        $file_paths = [];
        $thumbnail_paths = [];
        foreach($models as $model){
                $file_paths[] = $model->getFtpPath($setting);
                $thumbnail_paths[] = $model->getThumbnailFtpPath($setting);
        }
        $directory = $models?dirname($file_paths[0]):'';
        
        $result = $command->execute();
        
        if($result){
            foreach($file_paths as $path){
                @$ftp->delete($path);
            }
            foreach($thumbnail_paths as $path){
                @$ftp->delete($path);
            }
            if($directory && $ftp->isEmpty($directory)) 
                $ftp->rmdir($directory);
        }
        
        return $result;
    }


}
