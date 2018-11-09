<?php 
use app\components\CustomLinkPager;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $resultModel Array of \app\models\Media */
/* @var $dataProvider yii\data\SqlDataProvider */
/* @var $setting \app\models\Settings */
$isGuest = Yii::$app->user->isGuest;
$resultModel = $dataProvider->getModels();
for($i=0; $i < count($resultModel); $i++){
    $mediaModel = \app\models\Media::find()->where(['album_id'=>$resultModel[$i]['id']])->limit(4);
    if($isGuest)$mediaModel->andWhere(['is_public'=>1]);
    $mediaModel = $mediaModel->all();
    $resultModel[$i]['media'] = $mediaModel;
}
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
        margin: 0 0 30px 0;
        height:100px;
    }
    
    .content-list > .row > .thumbnail-slide-container > img{
        max-width:200px
    }
    .content-list > .row > .thumbnail-slide-container{
        width:200px;
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
    .thumbnail-slide-container{
        display:inline-block;
        overflow:hidden;
        height:inherit;
        width:inherit;
    }
    .thumbnail-slide{
        display:block;
        position:absolute;
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
        <?php $tags = explode(',',$model['tags']); ?>
        <div class="row">
            <a class="pull-left thumbnail-slide-container" href="#" style="display:inline-block">
                <?php foreach($model['media'] as $media): ?>
                <?php /* @var $media \app\models\Media */ ?>
                <img class="thumbnail-slide" src="<?=$media->getThumbnailHttpPath($setting)?>"/>
                <?php endforeach; ?>
            </a>
            <span class="caption pull-left">
                <a class="title" href="<?= Url::to(['/album/view','id'=>$model['id']]) ?>"><?=$model['name']?></a>
                <p><span>Tag: <?php foreach($tags as $tag): ?><a href="<?= Url::to(['search/index','q'=>$tag,'omediatag'=>1,'oalbumtag'=>1])?>"><?=$tag?></a>&nbsp;<?php endforeach; ?></span></p>
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
        <?php $tags = explode(',',$model['tags']); ?>
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
            <div class="thumbnail">
                <div class="thumbnail-img thumbnail-slide-container">
                    <a href="<?= Url::to(['/album/view','id'=>$model['id']]) ?>">
                        <?php foreach($model['media'] as $media): ?>
                        <?php /* @var $media \app\models\Media */ ?>
                        <img class="pull-left thumbnail-slide" src="<?=$media->getThumbnailHttpPath($setting)?>"/>
                        <?php endforeach; ?>
                    </a>
                </div>
                <div class="caption">
                    <h4><a href="<?= Url::to(['/album/view','id'=>$model['id']]) ?>"><?=$model['name']?></a></h4>
                    <p><span>Tag: <?php foreach($tags as $tag): ?><a href="<?= Url::to(['search/index','q'=>$tag,'omediatag'=>1,'oalbumtag'=>1])?>"><?=$tag?></a>&nbsp;<?php endforeach; ?></span></p>
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