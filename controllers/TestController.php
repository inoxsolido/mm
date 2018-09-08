<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

use app\models\Media;
use Yii;
use \yii\web\Controller;
use \yii\helpers\FileHelper;
use \yii\web\UploadedFile;
/**
 * Description of testController
 *
 * @author Ball
 */
class TestController extends Controller
{
    public function actionRelate(){
        $model = new Media;
        $model->media_type_id = 5;
        echo $model->mediaType->name;
    }
    public function actionOr(){
        $a = "";
        $b = "Hello";
        
        echo $a or die($b);
    }
    public function actionTransaction(){
        try{
            $model = new \app\models\Album;
            $model->name="ทหาร";
//            if(!$model->save())
//                throw new \Exception("Error while saving");
            $model->getIdByName("บก");
                
            echo 'done';
        } catch (\Exception $ex) {
            Yii::$app->response->statusCode=405;
            Yii::$app->response->statusText="Duplicate album";
            echo $ex->getMessage();
            
        }
    }
    
    public function actionFtpThai(){
        $setting = \app\models\Settings::find()->one();
        $ftp = new \app\components\FtpClient();
//        $ftp = new \yii2mod\ftp\FtpClient();
        $ftp->connect($setting->ftp_host);
        $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
        $ftp->pasv(true);
//        $create_path = $setting->ftp_part .'/'.  iconv("UTF-8","TIS-620","ภาษาไทย" ) ;
        $create_path = $setting->ftp_part .'/'.  "ภาษาไทย".'/'.  "ภาษาไทย๒"  ;
         $ftp->chdir($setting->ftp_part);
        $status = $ftp->make_directory($create_path);
        echo $status;
    }
    
    public function actionAssets(){
//        echo '<pre>';
//        print_r(Yii::$app->assetManager->bundles);
//        echo '</pre>';
        return $this->render('test');
    }
    public function actionQuery(){
        print_r((new \app\models\MediaType())->getExtensionAsString(['video', 'image']));
//        print_r((new \app\models\MediaType())->find()->select("extension")->where(['in', 'name',['video']])->asArray()->all());
    }
    public function actionAlias(){
        
        echo Yii::getAlias('@app').'<br/>';
        echo Yii::getAlias('@vendor').'<br/>';
        echo Yii::getAlias('@web').'<br/>';
        echo Yii::getAlias('@webroot').'<br/>';
        echo Yii::getAlias('@2amigos').'<br/>';
        
    }
    public function actionIndex(){die(var_dump($m_media = \app\models\MediaType::findOne(2)->getMedia()->andWhere(['album_id'=>null])->all()));}
    public function actionIndexA()
    {

        $sentence = "จะไปกินข้าวที่ร้านอาหาร I Love You";
        $dic = ["ฉัน", "ไป", "กินข้าว", "ร้านอาหาร", "ไก่ย่าง"];
//        $pattern = "/".implode("|",$dic)."/";
//        $pattern = "/";
//        foreach ($dic as $w){
//            $pattern .= "$w)|";
//        }
//        $pattern = substr($pattern,0,strlen($pattern)-1);
//        $pattern .= "/";
        $matched = "";


        $allword = \app\models\Dictionary::find()->select(['word', 'length'])->orderBy(['length' => SORT_DESC])->column();
        $pattern = "/" . implode("|", $allword) . "/";

        preg_match_all($pattern, $sentence, $prepared_words, PREG_OFFSET_CAPTURE);
        $x = preg_replace_callback($pattern, function($matched)
        {
            return str_repeat("|", strlen($matched[0]));
        }, $sentence);
//        foreach($prepared_words[0] as $w){
//            $len = strlen($w[0]);
//            $replacement = "asdasdasd";
//            $x = preg_replace("/$w[0]/",$replacement,$sentence);
//        }


        print_r($x);
        //capture unmatched en
        preg_match_all('/\|[a-zA-Z0-9]+/', $x, $unmatched_en, PREG_OFFSET_CAPTURE);
        preg_match_all('/\|?[ก-๙]+/', $x, $unmatched_th, PREG_OFFSET_CAPTURE);
        $y = array_map(function($value)
        {
            if (preg_match("/\|/", $value[0]))
            {
                return [[str_replace("|", "", $value[0]), $value[1] - 1]];
            }
            else
                return [[$value[0], $value[1]]];
        }, $unmatched_th[0]);
        ?><pre><?php
            echo 'pattern' . $pattern;
            echo '$prepared_words';
            print_r($prepared_words);
            echo '$unmatched_en';
            print_r($unmatched_en);
            echo '$unmatched_th';
            print_r($y);
            ?></pre><?php
        }

        public function actionSplit()
        {
            $search = "ฉันจะไปกินข้าวที่ร้านอาหาร I Love U";
            ?><pre><?php
        print_r(Yii::$app->word->split($search));
        ?></pre><?php
    }

    public function actionUpload()
    {
        if ( ! $_FILES)
            return $this->render('uploadform');
        //uploaded
        FileHelper::createDirectory("upload");
        $file = UploadedFile::getInstanceByName("file");

//        $iname = iconv("utf-8","TIS-620",$file->name);
        $success = $file->saveAs("upload/" . iconv("UTF-8", "WINDOWS-874", $file->name) . "." . $file->extension) or 0;
        print_r($file->getBaseName());
        print_r($success);
        print_r(Yii::getAlias("@webroot/upload"));
    }

    public function actionLen()
    {
        $str = "กก.กุ๊ก";
        echo 'strlen: ' . strlen($str);
        echo 'mb_strlen' . mb_strlen($str, 'UTF-8');
    }

    public function actionSpeed()
    {
        $time_start = microtime(true);
        $a = \app\models\Dictionary::find()->all();
        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start) / 60;

//execution time of the script
        echo '<b>Total Execution Time:</b> ' . $execution_time . ' Mins';
    }
     public $enableCsrfValidation = false;
    public function actionCron(){
        $m = \app\models\FrequencyWord::find()->where(['word'=>'test'])->one();
        if($m){
            $m->frequency += 1;
            $m->save();
        }
    }
    
    function uploadFileFTP($ftpServer, $ftpUsername, $ftpPassword, $remoteFile, $localFile)
    {
        // connect to ftp server
        $ftpConnID = ftp_connect($ftpServer);
        
        // login to ftp
        if (@ftp_login($ftpConnID, $ftpUsername, $ftpPassword))
        {
            // successfully connected
        }
        else
        {
            // Error to log-in in ftp
            return false;
        }
        if(ftp_put(ftp_put($ftpConnID, $remoteFile, $localFile, FTP_BINARY)))
        {
            echo "File uploaded successfully";
        }
        else
        {
            echo "Error to upload file on FTP server";
        }
        // close connection
        ftp_close($connection);
    }
    public function actionFtest(){
        $setting = \app\models\Settings::find()->one();
        $this->uploadFileFTP($setting->ftp_host, $setting->ftp_user, $setting->getRealFtpPassword(), '/home/project/mms/unassign/2017-08-04-13-39-08/1436344721-AA12-o.jpg', 'C:\\Users\\Ball\\Desktop\\1436344721-AA12-o.jpg');
    }

    public function actionFtpMkDirectory(){
        $ftp = new \app\components\FtpClient();
        $setting = \app\models\Settings::find()->one();
        $ftp->connect($setting->ftp_host);
        $ftp->login($setting->ftp_user, $setting->getRealFtpPassword());
        $ftp->pasv(true);
        $ftp->chdir($setting->ftp_part);
        $create_path = "/home/project/mms/Test/TestPrayud";
        echo dirname($create_path).'<br/>';
        echo dirname(dirname($create_path)).'<br/>';
//        return dirname($create_path);
        return $ftp->make_directory(strval($create_path));
    }

    public function actionMultipleWhere(){
        echo '<pre>';
        print_r(Media::find()->where(['id'=>['41','42']])->asArray()->all());
        echo '</pre>';
    }

    public function actionArrayKeyTest(){
        print_r(array_keys(['a'=>'asdasd','b'=>'zxczxc']));
        echo '<br/>';
        print_r(['a','b']);
    }

}
