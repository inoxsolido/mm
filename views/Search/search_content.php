<?php 
use app\components\CustomLinkPager;
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
    
    .content-list > div > a > img{
        float:left;
        max-width:200px
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
            <a href="#"><img class="pull-left" src="<?='http://'.$setting->ftp_host.$setting->http_part.'/'.$model['file_thumbnail_path']?>" /></a>
            <span class="caption pull-left">
                <a class="title" href="#"><?=$model['name']?></a>
                <p class="detail">
                    <?php if($model['album_id']):?>
                    From Album: <a href="#"><?=$model['album_name']?></a>
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
                <a href="#"><img src="<?='http://'.$setting->ftp_host.$setting->http_part.'/'.$model['file_thumbnail_path']?>" alt=""/></a>
                </div>
                <div class="caption">
                    <h4><a href="#"><?=$model['name']?></a></h4>
                    <p>Album: <a href="#"><?=$model['album_name']?></a></p>
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