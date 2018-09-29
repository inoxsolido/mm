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

        $model = Media::findOne($id);
        if ($model === null) {
            Yii::$app->response->setStatusCode(404);
            throw new \yii\web\NotFoundHttpException("Page Not Found.");
        } else {
            if (Yii::$app->request->isPost) {
                $model->load(Yii::$app->request->post());
                $setting = \app\models\Settings::getSetting();
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

    //not complete
    public function actionDelete($id){
        $media = $this->findModel($id);
        //backup file path and thumbnail path
        if($media->delete()){
            Yii::$app->getSession()->setFlash(
                    'success', "$name deleted"
            );
        }
        return $this->redirect(['index']);
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
        if (($model = User::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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
