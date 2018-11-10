<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
//models
use app\models\MediaSearch;
use app\models\Dictionary;
use app\models\Settings;

use yii\filters\AccessControl;
use app\components\AjaxFilter;


class SearchController extends Controller {

    public function behaviors() {
        return [
            [
                'class' => AjaxFilter::className(),
                'only' => [ 'search', 'suggest-word']
            ],
            'access' => [
                'class' => AccessControl::className(),
                
                'except'=> ['index', 'search', 'suggest-word', 'album'],
                'rules' => [
                    [
                        'allow'=>false,
                        'actions' => ['directory'],
                        'roles' => ['?']
                    ],                           
                ],
            ]
        ];
    }
    
    /**
     * This is Default Route to display all medias.
     * @return mixed List of media
     */
    public function actionIndex() {
        $params = Yii::$app->request->queryParams;
        
        $searchModel = new MediaSearch();
        $dataProvider = $searchModel->search($params);

        $setting = Settings::getSetting();
        return $this->render('/search/main', [
                    'dataProvider' => $dataProvider,
                    'setting' => $setting,
        ]);
    }
    
    /**
     * Ajax query to search media
     * @return string html content
     */
    public function actionSearch() {
        /* Data from request:
         * 1. query (text)
         * 2. condition (type, date(range), isAlbum)
         */
        $params = Yii::$app->request->post();
        $q = Yii::$app->request->post('q');
        $setting = Settings::getSetting();
        if ($q != "") {
            $freg_relation_rate = $setting->frequency_relation_rate;
            $related_word = [];
            $sql = "SELECT word1 as word,frequency FROM frequency_relation WHERE word2 LIKE '%$q%' AND frequency >= $freg_relation_rate \n"
                    . "UNION \n"
                    . "SELECT word2 as word,frequency FROM frequency_relation WHERE word1 LIKE '%$q%' AND frequency >= $freg_relation_rate \n"
                    . "ORDER BY frequency DESC";
            $related_word = Yii::$app->db->createCommand($sql)->queryColumn('word');
            $params['related_word'] = $related_word;
            //เพิ่ม frequency word
        }
        $searchModel = new MediaSearch();
        $dataProvider = $searchModel->search($params);
        $dataProvider->getPagination()->setPage(@$params['p']);
//        $queryParams = Yii::$app->request->queryParams;
//        Yii::$app->utility->debug($queryParams);
                
        return $this->renderPartial('/search/search_content', [
                    'dataProvider' => $dataProvider,
                    'setting' => $setting,
        ]);
    }
    /**
     * Display album element with search box
     * @return mixed
     */
    public function actionAlbum(){
        
        $q = Yii::$app->request->get('q');
        if(Yii::$app->request->isAjax){return 'ajaxajx';}
        else{
            $searchModel = new \app\models\AlbumSearch();
            $dataProvider = $searchModel->search($q);
            return $this->render('/search/main_album', [
                    'dataProvider' => $dataProvider,
                    'setting' => Settings::getSetting(),
            ]);
        }
    }
    /**
     * Display file in ftp-server by fetching file list from ftp
     * @return mixed
     * @throws \yii\web\HttpException
     */
    public function actionDirectory(){
        $path = Yii::$app->request->get('path');
        $path = str_replace("..","",$path);
        $path = preg_replace('/\/+/', '/', $path);
        if(strpos($path, '..'))
            $path='';
        
        $setting = Settings::getSetting();
        /* @var $setting Settings */
        $ftp = new \app\components\FtpClient();
        $ftp->connect($setting->ftp_host);
        $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
        $ftp->pasv(true);
        try{
            $list = $ftp->scanDir($setting->ftp_part.$path);
            return $this->render('directory', ['list'=>$list, 'path'=>$path]);
        } catch (\Exception $ex) {
            throw new \yii\web\HttpException('404', 'Page not found.');
        }
        
        
    }

    //ajax suggest-words
    //return as json
    public function actionSuggestWord() {
        $querySentence = Yii::$app->request->post("query");
        //find last word and sent suggest last word
        if ($querySentence) {//split sentence and get last word
            $words = Yii::$app->word->split($querySentence);
            $oneWordFlag = 0;
            if (count($words) > 1) {
                $lastword = $words[count($words) - 1];
            } else {
                $lastword = $words[0];
                $oneWordFlag = 1;
            }
            //ตัวอักษร/คำ ขึ้นต้น
            $result1 = Dictionary::find()->where(["LIKE", "word", $lastword . "%", false])->select("word")->limit(10)->asArray()->all();
            $result2_limit = max(0,10-count($result1));
            //ตัวอักษร/คำ อยู่ตรงในก็ได้
            $result2 = [];
            if($result2_limit > 0)
                $result2 = Dictionary::find()->where(["LIKE", "word", $lastword])->andWhere(["NOT IN", "word", $result1])->select("word")->limit($result2_limit)->asArray()->all();
            //merge arrays
            $merged = array_merge($result1, $result2);
            //2Dim to 1Dim Convert
            $oneDim = array_map('current', $merged);
            $previousWord = "";
            if ($oneWordFlag != 1) {
                //นำคำค้นหาก่อนหน้าทั้งหมดมาวางต่อกัน
                for ($i = 0; $i < count($words) - 1; $i++) {
                    $previousWord .= $words[$i];
                }

                $result = array_map(function($w) use ($previousWord) {
                    return $previousWord . $w;
                }, $oneDim);
            } else {
                $result = $oneDim;
            }

            $out = array_values($result);
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $out;
        }
    }

}
