<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
                    'model' => $this->findModel($id),
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
        if ($model->load(Yii::$app->request->post()))
        {

            \yii\helpers\FileHelper::createDirectory("uploads/" . $model->username . "/");
            $model->image_file = \yii\web\UploadedFile::getInstance($model, 'image_file');

            if ($model->image_file)
            {
                $model->image_path = "uploads/" . $model->username . "/" . Yii::$app->security->generateRandomString() . '.' . $model->image_file->extension;
                if ($model->validate())
                    $model->image_file->saveAs($model->image_path);
            }



            $model->save();
            Yii::$app->getSession()->setFlash(
                    'success', 'User Created'
            );
            return $this->redirect(['index']);
        } else
        {
            return $this->render('create', [
                        'model' => $model,
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

        if ($model->load(Yii::$app->request->post()))
        {
            \yii\helpers\FileHelper::createDirectory("uploads/" . $model->username . "/");
            $model->image_file = \yii\web\UploadedFile::getInstance($model, 'image_file');

            //check password change
            $oldPassword = $model->getOldAttribute('password');
            if ("" == Yii::$app->encryption->encryptUserPassword($model->password))
            {
                $model->password = $oldPassword;
            }

            if ($model->image_file)
            {
                $model->image_path = "uploads/" . $model->username . "/" . Yii::$app->security->generateRandomString() . '.' . $model->image_file->extension;
                if ($model->validate())
                    $model->image_file->saveAs($model->image_path);
            }

            $model->save();
            Yii::$app->getSession()->setFlash(
                    'success', 'User Changed'
            );
            return $this->redirect(['index']);
        } else
        {
            return $this->render('update', [
                        'model' => $model,
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
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash(
                'success', 'User Deleted'
        );
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

}
