<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                
                'only' => ['index' ,'create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create',  'delete'],
                        'roles' => ['@'],
                        'matchCallback' => function($rule, $action){
                            return Yii::$app->user->identity->getIsAdmin();
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['@'],
                        'matchCallback' => function($rule, $action){
                            if(Yii::$app->user->identity->getIsAdmin()) return true;
                            if(Yii::$app->request->get('id') != Yii::$app->user->getId()) return false;
                            return true;
                        }
                    ],
                            
                ],
            ]
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->setScenario('create');
        $selector = [];
        $query = \app\models\UserType::find();
        $selector = $query->all();
        if ($model->load(Yii::$app->request->post()))
        {

            \yii\helpers\FileHelper::createDirectory("uploads/");
            $model->image_file = \yii\web\UploadedFile::getInstance($model, 'image_file');
            
            $model->password = Yii::$app->encryption->encryptUserPassword($model->password);
            $model->password_confirm = Yii::$app->encryption->encryptUserPassword($model->password_confirm);
            
            if ($model->image_file)
            {
                $model->image_path = "uploads/".Yii::$app->security->generateRandomString() . '.' . $model->image_file->extension;
                while(file_exists($model->image_path)){
                    $model->image_path = "uploads/".Yii::$app->security->generateRandomString() . '.' . $model->image_file->extension;
                }
                if ($model->validate())
                    $model->image_file->saveAs($model->image_path);
            }


            if ($model->validate()){
                $model->save();
                Yii::$app->getSession()->setFlash(
                        'success', 'User Created'
                );
                return $this->redirect(['index']);
            }else{
                return $this->render('create', [
                        'model' => $model,
                        'selector' => $selector,
                ]);
            }
        } else
        {
            return $this->render('create', [
                        'model' => $model,
                        'selector' => $selector,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if(!Yii::$app->user->identity->getIsAdmin() && $id == Yii::$app->user->getId()){
            $model->setScenario('personal');
        }
        $selector = [];
        $query = \app\models\UserType::find();
        $users = User::find()->where(['!=', 'id', Yii::$app->user->getId()])->andWhere(['user_type_id'=>1])->count();
        /* @var $qadmin \app\models\UserType */
        if($users === 0)
            $query->where(['id'=>1]);
            
        $selector = $query->all();
        if ($model->load(Yii::$app->request->post()))
        {
            \yii\helpers\FileHelper::createDirectory("uploads/");
            $model->image_file = \yii\web\UploadedFile::getInstance($model, 'image_file');
            //check password change
            $encrypedPassword = Yii::$app->encryption->encryptUserPassword($model->password);
            $oldPassword = $model->getOldAttribute('password');
            $oldEncrytedPassword = Yii::$app->encryption->encryptUserPassword($oldPassword);
            if ($model->password != "" && $oldEncrytedPassword != $encrypedPassword)
                $model->password = $encrypedPassword;
            else
                $model->password = $oldPassword;
            $model->password_confirm = $model->password;
            
            if(!Yii::$app->user->identity->getIsAdmin() && $id == Yii::$app->user->getId()){
                $model->user_type_id = $model->getOldAttribute('user_type_id');
            }
            $validate = $model->validate();
            //check profile image change
            if ($model->image_file)
            {
                if ($validate){
                    if(!$model->image_file->saveAs($model->image_path)){
                        Yii::$app->getSession()->setFlash('error', 'Error while saving profile image.'); 
                        return $this->render('update', [
                            'model' => $model,
                            'selector'=>$selector,
                        ]);                       
                    }
                    
                }
            }
            if($model->getOldAttribute('user_type_id') ==1 && 
                    $model->user_type_id != 1 &&
                    User::find()->where(['!=', 'id', $model->id])->andWhere(['user_type_id'=>1])->count() == 0
                    ){
                Yii::$app->getSession()->setFlash('error', 'Require at least 1 account as administrator.'); 
                return $this->render('update', [
                    'model' => $model,
                    'selector'=>$selector,
                ]); 
            }
            if($validate){
                $model->save();
                Yii::$app->getSession()->setFlash(
                        'success', 'User Changed'
                );
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }
            else{
                return $this->render('update', [
                        'model' => $model,
                        'selector'=>$selector,
                ]);
            }
        } else
        {
            return $this->render('update', [
                        'model' => $model,
                        'selector'=>$selector,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $user = $this->findModel($id);
        if($user->user_type_id ==1 && 
            User::find()->where(['!=', 'id', $user->id])->andWhere(['user_type_id'=>1])->count() == 0){
            Yii::$app->getSession()->setFlash('error', 'Require at least 1 account as administrator.'); 
            return $this->goback();
        }
        $file_path = $user->image_path;
        if(file_exists($file_path) && !@unlink($file_path)){
//            Yii::$app->getSession()->setFlash('error', 'Error while delete profile image.'); 
//            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }
        
        $user->delete();
        Yii::$app->getSession()->setFlash(
                'success', 'User Deleted'
        );
        return $this->goback();
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

}
