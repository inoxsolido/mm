<?php
/* @var $this \yii\web\View */
/* @var $mediaDataProver \yii\data\ActiveDataProvider */
/* @var $album \app\models\Album */
use app\components\CustomLinkPager;
use yii\widgets\DetailView;
use yii\helpers\Html;

$mediaModel = $mediaDataProvider->getModels();
$this->title = $album->name;
//$this->params['breadcrumbs'][] = ['label' => 'Album', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>

<!--Media-->
<div class="content-table row" style="">
    <?php if(!$mediaModel):?>
    <div style="text-align:center"><span class="text-info">No media found.</span></div>
    <?php else: ?>
    <?php foreach($mediaModel as $model): ?>
    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
        <div class="thumbnail">
            <div class="thumbnail-img">
            <a href="#"><img src="<?='http://'.$setting->ftp_host.$setting->http_part.'/'.$model['file_thumbnail_path']?>" alt=""/></a>
            </div>
            <div class="caption">
                <h4><a href="#"><?=$model['name']?></a></h4>
                <p>Upload: <?=Yii::$app->utility->strDateReformat($model['file_upload_date'], 'd/m/Y H:i') ?></p>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php endif; ?>
</div>

<?php 
    echo CustomLinkPager::widget([
        'pagination' => $mediaDataProvider->getPagination(),
        'linkOptions'=>['class'=>'btn-page'],
        'hideOnSinglePage' => false,
        'template' => ' <div class="content-page-number row"><center><nav aria-label="Page navigation"><pager/></nav></center></div>'
    ]);
?>
