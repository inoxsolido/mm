<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;
/* @var $this \yii\web\View */
/* @var $content string */

app\assets\AppAsset::register($this);
dmstr\web\AdminLteAsset::register($this);

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>
<?php $this->beginPage()?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">
    <head>
        <meta charset="<?=Yii::$app->charset?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?=Html::csrfMetaTags()?>
        <title><?=Html::encode($this->title)?></title>
        <?php $this->head()?>
        <style>
            td{
                vertical-align: middle !important;
            }
        </style>
    </head>
    <body class="hold-transition skin-red-light sidebar-mini sidebar-collapse">
        <?php $this->beginBody()?>
        <div id="error-messages" class="alert alert-danger" style="opacity: 1; display: none"></div>
        <div id="success-messages" class="alert alert-success" style="opacity: 1; display: none"></div>
        <div id='loading' style='position:absolute;z-index: 2000; width:100%; height:100%; background-color: white; opacity: 0.25;display:none;text-align: center; vertical-align: middle; line-height: 50'>loading</div>
        <div class="wrapper">

            <?=$this->render('header.php', ['directoryAsset' => $directoryAsset])?>

            <?=$this->render('left.php', ['directoryAsset' => $directoryAsset])?>

            <?=$this->render('content.php', ['content' => $content, 'directoryAsset' => $directoryAsset])?>

        </div>

        <?php $this->endBody()?>
    </body>
</html>
<?php $this->endPage()?>

