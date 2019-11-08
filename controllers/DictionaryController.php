<?php

namespace app\controllers;

use Yii;
use app\models\Dictionary;
use app\models\DictionarySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * DictionaryController implements the CRUD actions for Dictionary model.
 */
class DictionaryController extends Controller
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
                
                'rules' => [
                    [
                        'allow' => true,
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
     * Lists all Dictionary models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DictionarySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Dictionary model.
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
     * Creates a new Dictionary model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Dictionary();
        $model->setScenario('single');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success',$model->word." was Added");
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionCreateMultiple(){
        $model = new Dictionary();
        
        if ($model->load(Yii::$app->request->post())) {
            $word_array = explode("\r\n", $model->word);
            $success = 0;
            ini_set('memory_limit', '-1');
            
            foreach($word_array as $word){
                $m = new Dictionary;
                $m->setScenario('single');
                $m->word = $word;
                if($success != 1)
                    $success = $m->save();
                else
                    $m->save();
            }
            
            
     
            if($success)
                Yii::$app->getSession()->setFlash('success',"The list was Added");
            else
                Yii::$app->getSession()->setFlash('warning',"The list already in database");
            return $this->redirect(['index']);
        } else {
            return $this->render('create_multiple', [
                'model' => $model,
            ]);
        }
    }
    
    /**
     * Updates an existing Dictionary model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success',$model->word." was Updated");
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Dictionary model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $word = $model->word;
        $model->delete();
        Yii::$app->getSession()->setFlash('success',$model->word." was deleted");
        return $this->goBack();
    }

    /**
     * Finds the Dictionary model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Dictionary the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Dictionary::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
