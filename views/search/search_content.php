<?php 
use app\components\CustomLinkPager;
use app\models\Media;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $resultModel Array of \app\models\Media */
/* @var $dataProvider yii\data\SqlDataProvider */
/* @var $setting \app\models\Settings */
$resultModel = $dataProvider->getModels();
?>
<!-- TABLE CONTENT -->
<style>
    .thumbnail > .thumbnail-img {
        height: 200px;
    }
    .thumbnail > .thumbnail-img > a > img {
        height: 200px;
    }
    
    .content-list > .row {
        margin: 0 0 30px 0
    }
    
    .content-list > div > img{
        float:left;
        width:200px
    }
    .content-list > .row > .caption{
        float:right;
        margin: 0 0 0 10px;
        padding: 0;
    }
    .content-list > .row > .caption > *{
        margin-top: 0;
    }
    .caption > .title{
        font-size: 18px;
        font-family: 'Source Sans Pro',sans-serif;
    }
    
    .btn .active{
        -moz-box-shadow: inset 0 3px 5px rgba(0,0,0,0.125);
        -webkit-box-shadow: inset 0 3px 5px rgba(0,0,0,0.125);
        box-shadow: inset 0 3px 5px rgba(0,0,0,0.125);
    }
    
    hr{
        margin-bottom: 5px;
    }
    .view-setting{
        margin-bottom: 20px;
    }
</style>
<hr/>
<div class="container-fluid">
    
    <!-- DISPLAY VIEW SETTINGS-->
    <div class="view-setting row">
        <div class="col-xs-6 col-xs-offset-6 col-sm-6 col-sm-offset-6 col-md-4 col-md-offset-8 col-lg-3 col-lg-offset-9">
            <div class="btn-group pull-right">
                <button id="btntable" class="btn btn-default"><span class="glyphicon glyphicon-th"></span></button>
                <button id="btnlist" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span></button>
            </div>
        </div>
    </div>
    
    <!-- TEMPLATE LIST-->
    <div class="content-list" style="display:none">
        <?php if(!@$resultModel):?>
        <div style="text-align:center"><span class="text-info">No result found.</span></div>
        <?php else: ?>
        <?php foreach($resultModel as $model): ?>
        <div class="row">
            <img class="pull-left" src="<?= Media::generateThumbnailHttp($model['file_thumbnail_path'], $setting) ?>" />
            <span class="caption pull-left">
                <a class="previewable title" title="<?= $model['name'] ?>" data-id="<?=$model['id']?>" data-name="<?= $model['name'] ?>" 
                   data-tags="<?= $model['tags'] ?>" 
                   data-album_name="<?= $model['album_name'] ?>" data-album_link="<?= Url::to(['/album/view','id'=>$model['album_id']])?>" 
                   data-poster="<?= Media::generateThumbnailHttp($model['file_thumbnail_path'], $setting) ?>" 
                   data-type="<?=$model['media_type_id']?>" 
                   data-flink="<?= Media::generateFileHttp($model['file_path'], $model['file_name'], $model['file_extension'], $setting) ?>" href="#<?=$model['id']?>"><?=$model['name']?></a>
                <p class="detail">
                    <?php if($model['album_id']):?>
                    From Album: <a href="<?= Url::to(['album/view','id'=>$model['album_id']])?>"><?=$model['album_name']?></a>
                    <?php endif; ?>
                    <br/>
                    Upload: <?=Yii::$app->utility->strDateReformat($model['file_upload_date'], 'd/m/Y H:i') ?>
                </p>
            </span>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- TEMPLATE TABLE-->
    <div class="content-table row" style="display:none">
        <?php if(!@$resultModel):?>
        <div style="text-align:center"><span class="text-info">No result found.</span></div>
        <?php else: ?>
        <?php foreach($resultModel as $model): ?>
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
            <div class="thumbnail">
                <div class="thumbnail-img">
                    <a class="previewable" data-id="<?=$model['id']?>" 
                       data-tags="<?= $model['tags'] ?>" 
                       data-name="<?= $model['name'] ?>" 
                       data-album_name="<?= $model['album_name'] ?>" 
                       data-album_link="<?= Url::to(['/album/view','id'=>$model['album_id']])?>" 
                       data-poster="<?= Media::generateThumbnailHttp($model['file_thumbnail_path'], $setting) ?>" 
                       data-type="<?=$model['media_type_id']?>" 
                       data-flink="<?= Media::generateFileHttp($model['file_path'], $model['file_name'], $model['file_extension'], $setting) ?>" 
                       href="#<?=$model['id']?>">
                        <img src="<?= Media::generateThumbnailHttp($model['file_thumbnail_path'], $setting) ?>" alt=""/>
                    </a>
                </div>
                <div class="caption">
                    <h4><a class="previewable" title="<?= $model['name'] ?>" data-id="<?=$model['id']?>" 
                           data-tags="<?= $model['tags'] ?>" 
                           data-name="<?= $model['name'] ?>" 
                           data-album_name="<?= $model['album_name'] ?>" 
                           data-album_link="<?= Url::to(['/album/view','id'=>$model['album_id']])?>" 
                           data-poster="<?= Media::generateThumbnailHttp($model['file_thumbnail_path'], $setting) ?>" 
                           data-type="<?=$model['media_type_id']?>" 
                           data-flink="<?= Media::generateFileHttp($model['file_path'], $model['file_name'], $model['file_extension'], $setting) ?>" 
                           href="#<?=$model['id']?>"><?=$model['name']?></a></h4>
                    <?php if($model['album_id']):?>
                    <p>Album: <a title="<?=$model['album_name']?>" href="<?=Url::to(['album/view','id'=>$model['album_id']])?>"><?=$model['album_name']?></a></p>
                    <?php else: ?>
                    <p>&nbsp;</p>
                    <?php endif; ?>
                    <p>Upload: <?=Yii::$app->utility->strDateReformat($model['file_upload_date'], 'd/m/Y H:i') ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
        
        
    </div>
    <?php 
    echo CustomLinkPager::widget([
        'pagination' => $dataProvider->getPagination(),
        'linkOptions'=>['class'=>'btn-page'],
        'hideOnSinglePage' => false,
        'template' => ' <div class="content-page-number row"><center><nav aria-label="Page navigation"><pager/></nav></center></div>'
    ]);
    ?>
   
    
</div><!-- CONTAINER -->