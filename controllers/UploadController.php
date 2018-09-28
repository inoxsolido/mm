<?php
namespace app\controllers;
use yii\web\Controller;

use app\models\MediaType;
use app\models\Media;
use app\models\Album;
use yii\web\Response;
use Exception;
use Yii;
use yii\web\UploadedFile;
use yii\helpers\Url;


class UploadController extends Controller{
    
    
    public function actionImage(){
        $album = new Album;
        $album->scenario = Album::SCENARIO_CREATE;
        $getAlbum = Yii::$app->request->get('album');
        if(Yii::$app->request->isAjax && Yii::$app->request->post('ajax')){
            $album->load(Yii::$app->request->post());
            Yii::$app->response->format = Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($album);
        }
        if(Yii::$app->request->isPost && count($_FILES)){
            $album->load(Yii::$app->request->post());
            $album->files = UploadedFile::getInstances($album, 'files');
            $thumbnails = Yii::$app->request->post('thumbnails');
            $i=0;$thumb_len=count($thumbnails);
            if(thumb_len !== count($album->files)){
                Yii::$app->response->statusCode=405;
                return "Number of thumbnails mismatch with a number of files.";
            }
            
            $setting = \app\models\Settings::getSetting();
            $ftp;//declare for exception catching
            $writen_filename = [];
            $transaction = Yii::$app->db->beginTransaction();
            try{
                if($getAlbum === 'new')
                    if(!$album->save())
                        throw new \Exception("An error was found while saving new album.");
                    else
                        $album_id = $album->id;
                else
                    $album_id = $album->getIdByName();
                
                
                $ftp = new \app\components\FtpClient();
                $ftp->connect($setting->ftp_host);
                $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
                $ftp->pasv(true);
                
                foreach ($album->files as $file) {
                    $media = new Media;
                    $media->updateFileDate();
                    $media->album_id = $album_id;
                    $media->name = preg_replace('/\.\w+$/', '', $file->name);//remove file extension
                    //Check Extension In MediaType
//                    $mediaType = MediaType::find()->where(['LIKE', 'extension', $file->file_extension])->one();
                    $media->media_type_id = 2; //fixed by user
                    //                  Type/Album name
                    $media->file_path = $media->mediaType->name . '/' . $album->name;
                    $media->file_name = $media->getNewFileName();
                    $media->file_extension = '.' . $file->getExtension();
                    
                    $thumbnails[$i];
                    $temp_path = "uploads/temp/" . Yii::$app->getSecurity()->generateRandomString(4);
                    $real_temp_path = Yii::getAlias("@realwebroot/$temp_path");
                    $real_temp_path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $real_temp_path);
                    while ( file_exists($temp_path) ) {
                        $temp_path = "uploads/temp/" . Yii::$app->getSecurity()->generateRandomString(4);
                        $real_temp_path = Yii::getAlias("@realwebroot/$temp_path");
                        $real_temp_path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $real_temp_path);
                    }
                    // store base64 to jpeg file
                    \file_put_contents($real_temp_path, $thumbnails[$i]);
                    $media->file_thumbnail_path = $media->mediaType->name.'/thumbnail_'. $model->file_name.'.jpeg';
//                    $media->file_thumbnail_path = $media->file_path;
                    $i++;
                    $media->is_public = 0;
                    if(!$media->save()){
                        throw new \Exception("An error was found while saving {$media->name}.". print_r($media->errors));
                    }
                    
                    $create_path = $setting->ftp_part .'/'. $media->mediaType->name .'/'. $album->name ;       
                    $ftp->chdir($setting->ftp_part);
                    $ftp->make_directory($create_path);
                    if(!$ftp->put($media->getFtpPath($setting), $file->tempName, FTP_BINARY))
                            throw new \Exception("An error was found while putting {$media->name} to File server.");
                    $ftp->chmod(0755, $media->getFtpPath($setting));
                    // put file(thumbnail) to server
                    if(!$ftp->put($model->getThumbnailFtpPath($setting), $real_temp_path, FTP_BINARY))
                            throw new \Exception("An error was found while putting thumbnail_{$media->name} to File server.");
                    $ftp->chmod(0755, $model->getThumbnailFtpPath($setting));
                    unlink($real_temp_path);
                    $writen_filename[] = $media->getFtpPath($setting);
                    $writen_filename[] = $media->getThumbnailFtpPath($setting);
                }
                $transaction->commit();
                return Url::to(['album/update', 'id' => $album_id]);
            } catch (\Exception $ex) {
                $transaction->rollback();
                if(!($ex instanceof \yii2mod\ftp\FtpException) && !empty($writen_filename)){
                    $folder = dirname($writen_filename[0]);
                
                    foreach($writen_filename as $fname){
                        $ftp->delete($fname);
                    }
                    if(!$ftp->isEmpty($folder)){
                        $ftp->rmdir($folder);
                    }
                }
                Yii::$app->response->statusCode = 405;
                return $ex->getMessage();
            } 
        }
        
        $list_album_name = \yii\helpers\ArrayHelper::map(Album::find()->all(), 'name', 'name');

        return $this->render('./uploadimageform', [
                    'model' => $album, 'getAlbum' => $getAlbum, 'list_album' => $list_album_name
        ]);
    }
    
    
    
    
    public function actionVideo(){
        $model = new Media();
        $model->scenario = 'video';
        if(Yii::$app->request->isAjax && Yii::$app->request->post('ajax') === 'w0'){
            $model->load(Yii::$app->request->post());
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
            
        }
        
        if(Yii::$app->request->isPost){
            
            $model->load(Yii::$app->request->post());
            //get files to model
            $model->media_file = UploadedFile::getInstance($model, 'media_file');
            
            $fileType = \app\models\MediaType::findOne(Yii::$app->request->post('type'));
            if (!$fileType){ Yii::$app->response->statusCode = 500; return "Parameter is missing."; }
            try{
                $setting = \app\models\Settings::getSetting();
                $ftp = new \app\components\FtpClient();
                $ftp->connect($setting->ftp_host);
                $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
                $ftp->pasv(true);
                Yii::$app->utility->debug($ftp);
                //CREATE TEMP FOLDER IN WEB SERVER
                \yii\helpers\FileHelper::createDirectory('uploads/temp/');
                
                //Fill attributes for validation
                $date = $model->updateFileDate();
                //Create data to file attributes
                $model->file_path = $fileType->name .'/';
                $model->file_name = $model->getNewFileName();
                $model->file_extension = '.' . $model->media_file->getExtension();
                
                
//                $mediaType = MediaType::find()->where(['LIKE', 'extension', $model->file_extension])->one();
//                $model->media_type_id = @mediaType?$mediaType->id:5;//if mediatype is found use it else 5 meaning Etc
                $model->media_type_id = 1;
                
                $create_path = $setting->ftp_part .'/'. $model->mediaType->name ;
                $ftp->chdir($setting->ftp_part);
                $ftp->make_directory($create_path);//prepare folder for file
                $ftp->put($model->getFtpPath($setting), $model->media_file->tempName, FTP_BINARY);
                $ftp->chmod(0777,$model->getFtpPath($setting));
                
                //thumbnail
//                echo $model->getThumbnailDecoded();
//                echo $model->thumbnail_from_video;
//                print_r($_POST);
//                print_r($model);
//                
//                die();
                
                if($model->getThumbnailDecoded()==null){
//                    echo 'เน€เธ�เน�เธฒเธ�เธ�'; die();
                    $model->thumbnail_file = UploadedFile::getInstance($model, 'thumbnail_file');
                    $model->file_thumbnail_path = $fileType->name .'/thumbnail_'. $model->file_name.'.'. $model->thumbnail_file->getExtension();
                    $real_temp_path = $model->thumbnail_file->tempName;
                    $ftp->put($model->getThumbnailFtpPath($setting), $real_temp_path, FTP_BINARY);
                    $ftp->chmod(0777, $model->getThumbnailFtpPath($setting));
                    
                }else{
//                    echo 'เน€เธ�เน�เธฒเธฅเน�เธฒเธ�'; die();
                    $model->file_thumbnail_path = $fileType->name .'/thumbnail_'. $model->file_name.'.jpeg';
                    $temp_path = "uploads/temp/" . Yii::$app->getSecurity()->generateRandomString(16);
                    $real_temp_path = Yii::getAlias("@realwebroot/$temp_path");
                    $real_temp_path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $real_temp_path);
                    while ( file_exists($temp_path) ) {
                        $temp_path = "uploads/temp/" . Yii::$app->getSecurity()->generateRandomString(16);
                        $real_temp_path = Yii::getAlias("@realwebroot/$temp_path");
                        $real_temp_path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $real_temp_path);
                    }
                    // store base64 to jpeg file
                    \file_put_contents($real_temp_path, $model->thumbnail_from_video);
                    // put file to server
                    $ftp->put(iconv("UTF-8","TIS-620",$model->getThumbnailFtpPath($setting)), $real_temp_path, FTP_BINARY);
                    $ftp->chmod(0777, $model->getThumbnailFtpPath($setting));
                    unlink($real_temp_path);
                }
                $model->scenario = "default";
                if (!$model->save()) {
                    Yii::$app->response->statusCode = 500;

                    return 'Upload fail';
                }else{
                    return Url::to(['media/media-edit', 'id' => $model->id]);
                }

                
                
            } catch (\Exception $ex) {
                Yii::$app->response->statusCode = 500;
                return $ex->getMessage();
            }
            
        }else{
            Yii::$app->view->title = "Upload Video";
            return $this->render('./uploadvideoform', ['model'=>$model]);
        }
    }

    
    public function actionOther(){
        $model = new Media();
        $model->scenario = Media::SCENARIO_OTHER;
        if(Yii::$app->request->isAjax && Yii::$app->request->post('ajax') === 'w0'){
            $model->load(Yii::$app->request->post());
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
            
        }
        if(Yii::$app->request->isPost){
            
            
            $model->load(Yii::$app->request->post());
            //get files to model
            $model->media_file = UploadedFile::getInstance($model, 'media_file');
            $model->thumbnail_file = UploadedFile::getInstance($model, 'thumbnail_file');
            
            try{
                $setting = \app\models\Settings::getSetting();
                $ftp = new \app\components\FtpClient();
                $ftp->connect($setting->ftp_host);
                $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
                $ftp->pasv(true);
                
                //CREATE TEMP FOLDER IN WEB SERVER
                \yii\helpers\FileHelper::createDirectory('uploads/temp/');
                
                //Fill attributes for validation
                $date = $model->updateFileDate();
                //Create data to file attributes
                
                $model->file_name = $model->getNewFileName();
                $model->file_extension = '.' . $model->media_file->getExtension();
                $mediaType = MediaType::find()->where(['LIKE', 'extension', $model->media_file->getExtension()])->one();
                if(!$mediaType) $mediaType = MediaType::find()->where(['id'=>5])->one();
                $model->media_type_id = $mediaType->id;//if mediatype is found use it else 5 meaning Etc
                $model->file_path = $mediaType->name .'/';
                
                
                $create_path = $setting->ftp_part .'/'. $model->mediaType->name ;
                $ftp->chdir($setting->ftp_part);
                $ftp->make_directory($create_path );//prepare folder for file
                $ftp->put($model->getFtpPath($setting), $model->media_file->tempName, FTP_BINARY);
                $ftp->chmod(0777,$model->getFtpPath($setting));
                
                //thumbnail

                $model->thumbnail_file = UploadedFile::getInstance($model, 'thumbnail_file');
                $model->file_thumbnail_path = $mediaType->name .'/thumbnail_'. $model->file_name.'.'. $model->thumbnail_file->getExtension();
                $real_temp_path = $model->thumbnail_file->tempName;
                $ftp->put($model->getThumbnailFtpPath($setting), $real_temp_path, FTP_BINARY);
                $ftp->chmod(0777, iconv("UTF-8","TIS-620",$model->getThumbnailFtpPath($setting)));
                    
                
                $model->scenario = "default";
                if (!$model->save()) {
                    Yii::$app->response->statusCode = 500;
                    return 'Upload fail';
                }else{
                    return $this->redirect(Url::to(['media/media-edit', 'id' => $model->id]));
                }

                
                
            } catch (Exception $ex) {
                Yii::$app->response->statusCode = 500;
                return $ex->getMessage();
            }
            
        }else{
            $type = Yii::$app->request->get('type');
            Yii::$app->view->title = "Upload $type";
            return $this->render("./uploadgeneralform", ['model'=>$model]);
        }
        
    }
}
