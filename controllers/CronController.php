<?php

namespace app\controllers;

use Yii;
use app\models\Dictionary;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * WordController implements the CRUD actions for FrequencyWord model.
 */
class CronController extends Controller
{  
    public function beforeAction($action)
    {
        // your custom code here, if you want the code to run before action filters,
        // which are triggered on the [[EVENT_BEFORE_ACTION]] event, e.g. PageCache or AccessControl

        if (!parent::beforeAction($action)) {
            return false;
        }
        return true;
        $_allow_ips = ['127.0.0.1'];
        $requestIp = Yii::$app->request->getUserIp();
        if(in_array($requestIp, $_allow_ips) || (Yii::$app->user->identity != NULL &&  Yii::$app->user->identity->getIsAdmin())){
            return true;
        }else{
            throw new ForbiddenHttpException("You have no permission to access this page", 403);
        }

    }


    public function actionCleanMediaWord(){
        ini_set('max_execution_time', 0);
        try{
            if(!Dictionary::clearNonExistMediaWord()){
                echo "ERROR Media Word cannot be clean";
            }else{
                echo "SUCCESS";
            }
            
        }catch(\Exception $e){
            echo "Exception Error: ".$e->getMessage();
        }
    }
}
