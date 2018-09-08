<?php

namespace app\controllers;

use Yii;
use app\models\MediaType;
use app\models\Settings;
use app\models\Media;
use app\models\MediaSearch;
use app\models\Album;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * MediaController implements the CRUD actions for Media model.
 */
class MediaController extends Controller
{

    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [

                ],
            ],
        ];
    }

    /**
     * Lists all Media models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MediaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionDeleteAlbum()
    {
        if (Yii::$app->request->isPost) {
            if (Yii::$app->user->isGuest !== true) {
                $album_id = Yii::$app->request->post("album_id");
                $album = Album::findOne($album_id);

                if (!$album) {
                    Yii::$app->response->statusCode = 400;
                    Yii::$app->response->statusText = "Album Id is missing!.";
                    return Yii::$app->response->send();
                }

                $media = $album->getMedia()->all();
                $setting = Settings::find()->one();
                $transaction = Yii::$app->db->beginTransaction();

                try {
                    $ftp = new \app\components\FtpClient();
                    $ftp->connect($setting->ftp_host);
                    $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
                    $ftp->pasv(true);

                    if($media){
                        $directory = dirname($media[0]->getFtpPath($setting));

                        foreach ($media as $m) {
                            /* @var $m Media */
                            $file_path = $m->getFtpPath($setting);
                            if (!$ftp->delete($file_path)) {
                                throw new Exception("Ftp delete Error");
                            }
                            $m->delete($file_path);
                        }
                        if (!$ftp->remove($directory))
                            throw new Exception("Ftp remove Error");
                    }
                    $album->delete();
                    $transaction->commit();
                } catch (Exception $ex) {
                    $transaction->rollBack();
                    Yii::$app->response->statusCode = 500;
                    return $ex->getMessage();
                }


            }else{
                Yii::$app->response->statusCode = 401;
                return Yii::$app->response->send();
            }
        } else {
            Yii::$app->response->statusCode = 405;
            return Yii::$app->response->send();
        }
    }

    public function actionDeleteSelectedMedia(){
        if(Yii::$app->request->isPost){
            if(Yii::$app->user->isGuest !== true){
                $media_id_set = Yii::$app->request->post("media_id_set");
                if(Empty($media_id_set)){
                    Yii::$app->response->statusCode = 405;
                    return Yii::$app->response->send();
                }
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $setting = Settings::find()->one();
                    $ftp = new \app\components\FtpClient();
                    $ftp->connect($setting->ftp_host);
                    $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
                    $ftp->pasv(true);

                    $media = Media::find()->where(['id'=>$media_id_set])->all();


                    foreach ($media as $m) {
                        /* @var $m Media */
                        $file_path = $m->getFtpPath($setting);
                        if($ftp->delete($file_path)){
                            $m->delete();
                        }
                    }
                    $transaction->commit();
                    Yii::$app->session->setFlash("success", "ลบข้อมูลสำเร็จ");
                }catch(Exception $e){
                    $transaction->rollBack();
                    Yii::$app->response->statusCode = 500;
                    Yii::$app->response->statusText = $e->getMessage();
                    return Yii::$app->response->send();
                }
            }else{
                Yii::$app->response->statusCode = 401;
                return Yii::$app->response->send();
            }
        }else{
            Yii::$app->response->statusCode = 405;
            return Yii::$app->response->send();
        }
    }

    public function actionAlbumEdit($id = '')
    {
        //Set default $album_id
        if ($id === '') $id = Yii::$app->request->get("id");
        //if still missing, return home
        if ($id === '') return $this->goHome();

        //get all media in album
        $m_album = Album::findOne($id);
        $m_media = '';
        if ($id == 0) { //if $id = 0: get media type image without album
            $m_media = \app\models\MediaType::findOne(2)->getMedia()->andWhere(['album_id' => null])->all();
        }else if($m_album === null){
            Yii::$app->response->setStatusCode(404);
            throw new \yii\web\NotFoundHttpException("Page Not Found.");
        }else {
            $m_media = $m_album->getMedia()->orderBy(['file_upload_date' => SORT_ASC])->all();
        }

        return $this->render("album-update-form", [
            'album' => $m_album,
            'media' => $m_media
        ]);
    }

    public function actionUpdateAlbum(){
        if(Yii::$app->request->isPost){
            if(!Yii::$app->user->isGuest){
                $media_set = Yii::$app->request->post("media_set");
                $album_data = Yii::$app->request->post("album_data");
                /* @var $setting \app\models\Settings */
                $setting = \app\models\Settings::find()->one();
                $ftp = new \app\components\FtpClient();

                if($album_data){//update Album Data
                    $album = Album::findOne($album_data['id']);
                    if(!$album){//Incorrect Album Id
                        Yii::$app->response->statusCode = 405;
                        return "Incorrect Album Id";
                    }
                    //Change Album Name
                    if($album->name != $album_data['name']){ //Changing
                        //Check duplicate new name with old albums
                        $album->name = $album_data['name'];
                        if(!$album->validate(['name'])){ // new name is duplicated
                            Yii::$app->reponse->statusCode = 405;
                            return "Album name is exist";
                        }else{//Rename folder on FTP Server
                            $ftp->connect($setting->ftp_host);
                            $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
                            $ftp->pasv(true);
                            $oldFolderName = $setting->ftp_part.'/'.$album->getOldAttribute('name');
                            $newFolderName = $setting->ftp_part.'/'.$album->name;


                            if(!$ftp->rename($oldFolderName,$newFolderName)){//return if cant rename
                                $ftp->close();
                                Yii::$app->response->statusCode = 405;
                                return "ไม่สามารถเปลี่ยนชื่ออัลบั้มภายใน FTP Server ได้";
                            }else
                                $ftp->close();
                        }
                    }
                    $album->tags = $album_data['tags'];
                    if(!$album->save()){
                        Yii::$app->response->statusCode = 500;
                        return "บันทึกข้อมูลอัลบั้มไม่สำเร็จ";
                    }else{
                        if($media_set) {
                            $ftp->connect($setting->ftp_host);
                            $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
                            $ftp->pasv(true);

                            //re struct to media_set['id']['name']['tags']['is_public']
                            $media_id_set = [];
                            $new_media_set = [];
                            foreach($media_set as $ms){
                                $media_id_set[] = $ms[0];
                                $new_media_set[$ms[0]] = [];
                                $new_media_set[$ms[0]]['name'] = $ms[1];
                                $new_media_set[$ms[0]]['tags'] = $ms[2];
                                $new_media_set[$ms[0]]['is_public'] = $ms[3];
                            }
                            $media_set = $new_media_set;

                            unset($new_media_set);
                            //-----
//                            $media_id_set = array_keys($media_set);
//                            // Remove x From $media_set because javascript need to add character in index of array to prevent auto index creation on javascript
//                            $media_id_set = array_map(function($e){
//                                return intval(str_replace('x','',$e));
//                            },$media_id_set);
                            $media = Media::find()->where(['id' => $media_id_set])->all();

                            foreach ($media as $m) {
                                /* @var $m Media */

                                $m->tags = $media_set[$m->id]['tags'];
                                $m->is_public = $media_set[$m->id]['is_public'];

                                $old_name = $m->name;
                                $oldFtpPath = $m->getFtpPath($setting);
                                if($m->name != $old_name){
                                    $m->name = $media_set[$m->id]['name'];

                                    $m->file_name = $m->getNewFileName();
                                    $newFtpPath = $m->getFtpPath();
                                    if($ftp->rename($oldFtpPath,$newFtpPath)){
                                        if(!$m->save()){
                                            Yii::$app->response->statusCode = 500;
                                            return "ไม่สามารถบันทึกไฟล์ $old_name ได้";
                                        }
                                    }else{
                                        Yii::$app->response->statusCode = 500;
                                        return "ไม่สามารถเปลี่ยนชื่อไฟล์ $old_name ได้";
                                    }

                                }else{
                                    if(!$m->save()){
                                        Yii::$app->response->statusCode = 500;
                                        return "ไม่สามารถบันทึกไฟล์ $old_name ได้";
                                    }
                                }
                            }
                            return "บันทึกข้อมูลสำเร็จ";
                        }else{
                            Yii::$app->response->statusCode = 405;
                            return "Media Data is missing";
                        }
                    }
                }else{
                    Yii::$app->response->statusCode = 405;
                    return "Album Data is missing";
                }
            }else{
                Yii::$app->response->statusCode = 401;
                return Yii::$app->response->send();
            }
        }else{
            Yii::$app->response->statusCode = 405;
            return Yii::$app->response->send();
        }
    }

    public function actionCheckAlbumName(){
        if(Yii::$app->request->isPost){
            if(Yii::$app->user->isGuest !== true){
                $album_id = Yii::$app->request->post("album_id");
                $album_name = Yii::$app->request->post("album_name");
                $album = Album::findOne($album_id);
                if($album){
                    $album->name = $album_name;
                    return $album->validate(['name'])?'ok':'not';
                }else{
                    Yii::$app->response->statusCode = 405;
                    return "Incorrect album id";
                }
            }else{
                Yii::$app->response->statusCode = 401;
                return Yii::$app->response->send();
            }
        }else{
            Yii::$app->response->statusCode = 405;
            return Yii::$app->response->send();
        }
    }

    public function actionMediaEdit($id) {
        //Set default $media->id
        if ($id === '')
            $id = Yii::$app->request->get("id");
        //if still missing, return home
        if ($id === '')
            return $this->goHome();

        $model = Media::findOne($id);
        if ($model === null) {
            Yii::$app->response->setStatusCode(404);
            throw new \yii\web\NotFoundHttpException("Page Not Found.");
        } else {
            if (Yii::$app->request->isPost) {
                $model->load(Yii::$app->request->post());
                $setting = \app\models\Settings::find()->one();
                $old_file_path_full = $model->getFtpPath($setting);
                $old_file_thumbnail_path_full = $model->getThumbnailFtpPath($setting);
                $isNameChanged = $model->isAttributeChanged('name');
                $isAlbumChanged = $model->isAttributeChanged("album_id");
                $album_name = $model->album_id?$model->album->name:"";

                if($isAlbumChanged){ //media_file only
                    $album_name = Album::findOne($model->album_id);
                    if($album_name) $album_name = $album_name->name; else $album_name = "";
                    $model->file_path = "{$model->mediaType->name}/{$album_name}";
                }
                if($isNameChanged){//filename & thumbnail path
                    //change filename
                    $model->file_name = $model->getNewFileName(false);
                    //change file thumbnail path

                    preg_match('/\.\w+$/',$model->file_thumbnail_path, $ext);//get old extension
                    if(!count($ext)) $ext[0] = "";// prevent error: array convert to string
                    $model->file_thumbnail_path = "{$model->mediaType->name}/";
                    if($model->album_id){
                        $model->file_thumbnail_path .= "{$album_name}/";
                    }
                    $model->file_thumbnail_path .= "thumbnail_{$model->file_name}{$ext[0]}";
                }
                //if new thumbnail was upload
                $file = UploadedFile::getInstance($model, 'thumbnail_file');


                $transaction = Media::getDb()->beginTransaction();
                try{
                    if($model->save()){
                        $ftp = new \app\components\FtpClient();
                        $ftp->connect($setting->ftp_host);
                        $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
                        $ftp->pasv(true);

                        $ftp->chdir($setting->ftp_part);
                        if($isNameChanged == false && $file){
                            //delete old file
                            if(!$ftp->delete($old_file_thumbnail_path_full)){
                                throw new \Exception("Error while deleting.");
                            }

                        }
                        if($isNameChanged || $isAlbumChanged){

                            //rename file & thumbnail
                            if(!(
                                $ftp->rename($old_file_path_full, $model->getFtpPath($setting))
                                && $ftp->rename($old_file_thumbnail_path_full, $model->getThumbnailFtpPath($setting))
                            )){
                                throw new \Exception("Error while renaming.");
                            }
                            $ftp->chmod(0777, $model->getFtpPath($setting));
                            $ftp->chmod(0777, $model->getThumbnailFtpPath($setting));
                        }
                        if($file){
                            $real_temp_path = $file->tempName;
                            if(!$ftp->put($model->getThumbnailFtpPath($setting), $real_temp_path, FTP_BINARY)){
                                throw new \Exception("Error while writing new file.");
                            }
                            $ftp->chmod(0777, $model->getThumbnailFtpPath($setting));
                        }
                    }

                    $transaction->commit();
                } catch (\Exception $ex) {
                    $transaction->rollBack();
                    Yii::$app->response->statusCode = 500;
                    return $ex->getMessage();
                }

                return Url::to("media/detail",['id'=>$id]);
            } else {
                $albumList = Album::find()->all();
                $albumList = \yii\helpers\ArrayHelper::map($albumList, 'id', 'name');
                return $this->render("media-edit-form", ['model' => $model, 'albumList' => $albumList]);
            }
        }
    }

    public function actionList(){
        $searchModel = new MediaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    private function actionDummy(){
        if(Yii::$app->request->isPost){
            if(Yii::$app->user->isGuest !== true){

            }else{
                Yii::$app->response->statusCode = 401;
                return Yii::$app->response->send();
            }
        }else{
            Yii::$app->response->statusCode = 405;
            return Yii::$app->response->send();
        }

    }

}
