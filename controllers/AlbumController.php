<?php

namespace app\controllers;

use Yii;
use app\models\Settings;
use app\models\Album;
use app\models\AlbumSearch;
use app\models\Media;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\components\AjaxFilter;

/**
 * AlbumController implements the CRUD actions for Album model.
 */
class AlbumController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => AjaxFilter::className(),
                'only' => ['check-album-name', 'delete-selected-media']
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'check-album-name' => ['POST'],
                    'delete' => ['POST'],
                    'delete-selected-media' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'except'=>['view'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['delete', 'delete-selected-media', 'detail', 'index', 'update'],
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * Lists all Album models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AlbumSearch();
        $dataProvider = $searchModel->filter(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Album model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id='')
    {
        if(Yii::$app->request->isPost){
            
                $media_set = Yii::$app->request->post("media_set");
                $album_data = Yii::$app->request->post("album_data");
                /* @var $setting \app\models\Settings */
                $setting = \app\models\Settings::getSetting();
                $ftp = new \app\components\FtpClient();
                
                $new_file_path = '';
                if($album_data){//update Album Data
                    $album = Album::findOne($album_data['id']);
                    if(!$album){//Incorrect Album Id
                        Yii::$app->response->statusCode = 400;
                        return "Incorrect Album Id";
                    }
                    $ftp->connect($setting->ftp_host);
                    $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
                    $ftp->pasv(true);
                    //Change Album Name
                    if($album->name != $album_data['name']){ //Changing
                        //Check duplicate new name with old albums
                        $album->name = $album_data['name'];
                        if(!$album->validate(['name'])){ // new name is duplicated
                            Yii::$app->reponse->statusCode = 400;
                            return "Album name is exist";
                        }else{//Rename folder on FTP Server
                            $oldFolderName = $setting->ftp_part.'/Image/'.$album->getOldAttribute('name');
                            $newFolderName = $setting->ftp_part.'/Image/'.$album->name;


                            if($ftp->isDir($oldFolderName) && !$ftp->rename($oldFolderName,$newFolderName)){//return if cant rename
                                Yii::$app->response->statusCode = 500;
                                return "ไม่สามารถเปลี่ยนชื่ออัลบั้มภายใน FTP Server ได้";
                            }
                        }
                    }
                    $album->tags = $album_data['tags'];
                    if(!$album->save()){
                        Yii::$app->response->statusCode = 500;
                        return "บันทึกข้อมูลอัลบั้มไม่สำเร็จ";
                    }else{
                        if($media_set) {
//                            $ftp->connect($setting->ftp_host);
//                            $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
//                            $ftp->pasv(true);
                            
                            //re struct to media_set['id']['attribute']
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
   
                            $media = Media::find()->where(['id' => $media_id_set])->all();
                            $new_file_path = 'Image/'.$album->name;
                            foreach ($media as $m) {
                                /* @var $m Media */
                                //assign
                                $m->tags = $media_set[$m->id]['tags'];
                                $m->is_public = $media_set[$m->id]['is_public'];
                                $m->file_path = $new_file_path;
                                //backup 
                                $old_name = $m->name;
                                $oldFtpPath = $m->getFtpPath($setting);
                                $oldThumbnail = $m->getThumbnailFtpPath($setting);
                                
                                $m->name = $media_set[$m->id]['name'];
                                if($m->name != $old_name){
                                    
                                    $m->file_name = $m->getNewFileName();
                                    $m->file_thumbnail_path = 'thumbnails/thumbnail_'. $m->file_name.'.jpeg';
                                    $newFtpPath = $m->getFtpPath($setting);
                                    $newThumbnail = $m->getThumbnailFtpPath($setting);
                                    if($m->validate() && $ftp->rename($oldFtpPath,$newFtpPath) && $ftp->rename($oldThumbnail, $newThumbnail)){
                                        
                                        if(!$m->save(false)){
                                            Yii::$app->response->statusCode = 500;
                                            return "ไม่สามารถบันทึกไฟล์ $old_name ได้";
                                        }
                                    }else{
                                        //try to rename back
                                        $ftp->rename($newFtpPath, $oldFtpPath);
                                        $ftp->rename($newThumbnail, $oldThumbnail);
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
                            if($album->getMedia()->count()){
                                Yii::$app->response->statusCode = 400;
                                return "Media Data is missing";
                            }else{
                                return "บันทึกข้อมูลสำเร็จ";
                            }
                        }
                    }
                }else{
                    Yii::$app->response->statusCode = 400;
                    return "Album Data is missing";
                }
            }else{//Request type: GET
                $m_album = $this->findModel($id);

                $m_media = '';
                $m_media = $m_album->getMedia()->orderBy(['file_upload_date' => SORT_ASC])->all();

                return $this->render("/album/album-update-form", [
                    'album' => $m_album,
                    'media' => $m_media
                ]);
            }
    }
    /**
     * Ajax function to validate name
     * @return mixed
     */
    public function actionCheckAlbumName(){
        if(Yii::$app->request->isPost){
            $album_id = Yii::$app->request->post("album_id");
            $album_name = Yii::$app->request->post("album_name");
            $album = Album::findOne($album_id);
            if($album){
                $album->name = $album_name;
                return $album->validate(['name'])?'ok':'not';
            }else{
                Yii::$app->response->setStatusCode(400);
                return "Incorrect album id";
            }
        }else{
            Yii::$app->response->statusCode = 405;
            return Yii::$app->response->statusText;
        }
    }
    /**
     * Ajax to delete Media by given media id
     * 
     * @return mixed
     */
    public function actionDeleteSelectedMedia(){
        if(Yii::$app->request->isPost){
            if(Yii::$app->user->isGuest !== true){
                $media_id_set = Yii::$app->request->post("media_id_set");
                if(Empty($media_id_set)){
                    Yii::$app->response->statusCode = 400;
                    return 'Media set is missing!.';
                }
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    Media::deleteAll(['id' => $media_id_set]);
                    $transaction->commit();
                    Yii::$app->session->setFlash("success", "ลบข้อมูลสำเร็จ");
                }catch(Exception $e){
                    $transaction->rollBack();
                    Yii::$app->response->statusCode = 500;
                    Yii::$app->response->statusText = $e->getMessage();
                    return Yii::$app->response->statusText;
                }
            }else{
                Yii::$app->response->setStatusCode(401);
                return Yii::$app->response->statusText;
            }
        }else{
            Yii::$app->response->setStatusCode(405);
            return Yii::$app->response->statusText;
        }
    }
    /**
     * Deletes an existing Album model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $album = $this->findModel($id);
        if (!$album) {
            Yii::$app->response->statusCode = 400;
            Yii::$app->response->statusText = "Album Id is missing!.";
            return 'Album Id is missing!.';
        }

        $media = $album->getMedia()->all();
        $setting = Settings::getSetting();
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $ftp = new \app\components\FtpClient();
            $ftp->connect($setting->ftp_host);
            $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
            $ftp->pasv(true);

            if($media){
                $directory = dirname($media[0]->getFtpPath($setting));

                $deleteResult = Media::deleteAll(['album_id'=>$album->id]);

            }
            $album_name = $album->name;
            $album->delete();
            Yii::$app->session->setFlash('Success', "Album: $album_name deleted.");
            $transaction->commit();
        } catch (Exception $ex) {
            $transaction->rollBack();
            Yii::$app->response->statusCode = 500;
            return $ex->getMessage();
        }
        
        return $this->redirect(['index']);  
    }
    
    
    /**
     * Displays a single Album model with detail.
     * @param integer $id
     * @return mixed
     */
    public function actionDetail($id)
    {
        $album = $this->findModel($id);
        $mediaDataProvider = new \yii\data\ActiveDataProvider([
            'query' => Media::find()->where(['album_id'=>$album->id])->orderBy(['file_upload_date'=>SORT_ASC]),
            'pagination' => [
                'pageParam' => 'p',
                'pageSize' => 20,
                'pageSizeParam' => false,
            ],
        ]);
        return $this->render('detail', [
            'album' => $album,
            'mediaDataProvider'=>$mediaDataProvider,
            'setting' => Settings::getSetting()
        ]);
    }
    /**
     * Display a single Album model without detail.
     * @param int $id
     * @return mixed
     */
    public function actionView($id){
        $album = $this->findModel($id);
        $mediaDataProvider = new \yii\data\ActiveDataProvider([
            'query' => Media::find()->where(['album_id'=>$album->id])->orderBy(['file_upload_date'=>SORT_ASC]),
            'pagination' => [
                'pageParam' => 'p',
                'pageSize' => 20,
                'pageSizeParam' => false,
            ],
        ]);
        return $this->render('view', [
            'album' => $album,
            'mediaDataProvider'=>$mediaDataProvider,
            'setting' => Settings::getSetting()
        ]);
    }
    
    
    /**
     * Finds the Album model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Album the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Album::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
