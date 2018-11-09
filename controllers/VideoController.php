<?php
namespace app\controllers;
use app\models\MediaType;
use app\models\Settings;
use app\models\Media;
use app\models\Album;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use Yii;
use yii\web\UploadedFile;



class VideoController extends Controller{
    
    
    public function actionUpload(){
        $model = new Media();
        $model->scenario = 'create';
        if(Yii::$app->request->isAjax && Yii::$app->request->post('ajax') === 'w0'){
            $model->load(Yii::$app->request->post());
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return \yii\widgets\ActiveForm::validate($model);
            
        }
        
        if(Yii::$app->request->isPost){
            
            //thumbnail from fileinput
            //thumbnail from thumbnail
            $model->load(Yii::$app->request->post());
            //get files to model
            $model->media_file = UploadedFile::getInstance($model, 'media_file');
            
            $fileType = \app\models\MediaType::findOne(Yii::$app->request->post('type'));
                if (!$fileType){ Yii::$app->response->statusCode = 500; return "Parameter is missing."; }
            try{
                $setting = \app\models\Settings::find()->one();
                $ftp = new \app\components\FtpClient();
                $ftp->connect($setting->ftp_host);
                $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
                $ftp->pasv(true);
                
                //CREATE TEMP FOLDER IN WEB SERVER
                \yii\helpers\FileHelper::createDirectory('uploads/temp/');
                
                //Fill attributes for validation
                $date = $model->updateFileDate();
                //Create data to file attributes
                $model->file_path = $fileType->name .'/';
                $model->file_name = $model->getNewFileName();
                $model->file_extension = '.' . $model->media_file->getExtension();
                
                
                $mediaType = MediaType::find()->where(['LIKE', 'extension', $model->file_extension])->one();
                $model->media_type_id = @mediaType?$mediaType->id:5;//if mediatype is found use it else 5 meaning Etc
                
                $create_path = $setting->ftp_part .'/'. $model->mediaType->name ;
                $ftp->chdir($setting->ftp_part);
                $ftp->make_directory($create_path);//prepare folder for file
                $ftp->put($model->getFtpPath($setting), $model->media_file->tempName, FTP_BINARY);
                $ftp->chmod(0777, $model->getFtpPath($setting));
                
                //thumbnail
//                echo $model->getThumbnailDecoded();
//                echo $model->thumbnail_from_video;
//                print_r($_POST);
//                print_r($model);
//                
//                die();
                
                if($model->getThumbnailDecoded()==null){
//                    echo 'เข้าบน'; die();
                    $model->thumbnail_file = UploadedFile::getInstance($model, 'thumbnail_file');
                    $model->file_thumbnail_path = $fileType->name .'/thumbnail_'. $model->file_name.'.'. $model->thumbnail_file->getExtension();
                    $real_temp_path = $model->thumbnail_file->tempName;
                    $ftp->put($model->getThumbnailFtpPath($setting), $real_temp_path, FTP_BINARY);
                    $ftp->chmod(0777, $model->getThumbnailFtpPath($setting));
                    
                }else{
//                    echo 'เข้าล่าง'; die();
                    $model->file_thumbnail_path = $fileType->name .'/thumbnail_'. $model->file_name.'.png';
                    $temp_path = "uploads/temp/" . Yii::$app->getSecurity()->generateRandomString(16);
                    $real_temp_path = Yii::getAlias("@realwebroot/$temp_path");
                    $real_temp_path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $real_temp_path);
                    while ( file_exists($temp_path) ) {
                        $temp_path = "uploads/temp/" . Yii::$app->getSecurity()->generateRandomString(16);
                        $real_temp_path = Yii::getAlias("@realwebroot/$temp_path");
                        $real_temp_path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $real_temp_path);
                    }
                    // store base64 to png file
                    \file_put_contents($real_temp_path, $model->thumbnail_from_video);
                    // put file to server
                    $ftp->put($model->getThumbnailFtpPath($setting), $real_temp_path, FTP_BINARY);
                    $ftp->chmod(0777, $model->getThumbnailFtpPath($setting));
                    unlink($real_temp_path);
                }
                $model->scenario = "default";
                if (!$model->save()) {
                    Yii::$app->response->statusCode = 500;

                    return 'Upload fail';
                }else{
                    return $this->redirect(Url::to(['media/edit', 'id' => $model->id]));
                }

                
                
            } catch (Exception $ex) {
                Yii::$app->response->statusCode = 500;
                return $ex->getMessage();
            }
            
        }else
        return $this->render('../media/uploadvideoform', ['model'=>$model]);
    }
}
