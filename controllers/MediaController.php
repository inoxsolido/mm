<?php

namespace app\controllers;

use Yii;
use app\models\Settings;
use app\models\Media;
use app\models\MediaSearch;
use app\models\Album;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use app\components\AjaxFilter;

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
            [
                'class' => AjaxFilter::className(),
                'only' => ['delete-selected-media']
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete'=>['POST'],
                    'delete-selected-media' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                
                'rules' => [
                    [
                        'allow' => true,
                        'action' => ['index', 'media-edit', ],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete', 'delete-selected-media'],
                        'roles' => ['@'],
                        'matchCallback' => function($rule, $action){
                            return Yii::$app->user->identity->getIsAdmin();
                        }
                    ],                            
                ],
            ]
        ];
    }

    /**
     * Lists all Media models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MediaSearch();
        $dataProvider = $searchModel->filter(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMediaEdit($id) {
        //Set default $media->id
        if ($id === '')
            $id = Yii::$app->request->get("id");
        //if still missing, return home
        if ($id === '')
            return $this->goHome();

        $model = $this->findModel($id);
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $setting = Settings::getSetting();
            $old_file_path_full = $model->getFtpPath($setting);
            $isNameChanged = $model->isAttributeChanged('name');
            $isAlbumChanged = $model->isAttributeChanged("album_id");
            $album_name = $model->album_id?$model->album->name:"";

            if($isAlbumChanged){ //media_file only
                $album_name = Album::findOne($model->album_id);
                if($album_name) $album_name = $album_name->name; else $album_name = "";
                $model->file_path = "{$model->mediaType->name}/{$album_name}";
            }
            if($isNameChanged){//filename
                //change filename
                $model->file_name = $model->getNewFileName(false);
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

                        //rename file
                        if(!$ftp->rename($old_file_path_full, $model->getFtpPath($setting))){
                            throw new \Exception("Error while renaming.");
                        }
                        $ftp->chmod(0755, $model->getFtpPath($setting));
                    }
                    if($file){//new thumbnail was upload
                        $real_temp_path = $file->tempName;
                        if(!$ftp->put($model->getThumbnailFtpPath($setting), $real_temp_path, FTP_BINARY)){
                            throw new \Exception("Error while writing new file.");
                        }
                        $ftp->chmod(0755, $model->getThumbnailFtpPath($setting));
                    }
                }
                $transaction->commit();
            } catch (\Exception $ex) {
                $transaction->rollBack();
                Yii::$app->response->statusCode = 500;
                return $ex->getMessage();
            }

            return Url::to("media/detail",['id'=>$id]);
        } else { //GET request
            $albumList = Album::find()->all();
            $albumList = \yii\helpers\ArrayHelper::map($albumList, 'id', 'name');
            return $this->render("media-edit-form", ['model' => $model, 'albumList' => $albumList]);
        }
    }

    /**
     * Post to delete Media by given id
     * @param integer $id
     * @return mixed redirect to index
     */
    public function actionDelete($id){
        $media = $this->findModel($id);
        //backup file path and thumbnail path
        $name = $media->name;
        if($media->delete()){
            Yii::$app->getSession()->setFlash(
                    'success', "$name deleted"
            );
        }
        return $this->goBack();
    }
    
    /**
     * Ajax to delete Media by given media id
     * 
     * @return mixed
     */
    public function actionDeleteSelectedMedia(){
 
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
    }
    
    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Media::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
