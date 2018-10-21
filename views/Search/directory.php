<?php 
use yii\helpers\Url;

?>

<style>
    .image{
        width:50px;
    }
    .highlight:hover{
        background-color: #ccc;
        border: #aaa solid 1px;
    }
    .highlight:active{
        background-color: #aaa;
    }
    .row{
        border-bottom: #eee solid 1px;
        padding: 15px;
    }
</style>
<!--header-->
<div class="row">
    <div class="col-sm-1 text-right">
        #
    </div>
    <div class="col-sm-5 text-left">
        Name
    </div>
    <div class="col-sm-3 text-left">
        Date modified
    </div>
    <div class="col-sm-3 text-left">
        Size
    </div>
</div>
<!--back-->
<?php if($path != ''): ?>
<a href="<?=!empty(Yii::$app->request->referrer) ? Yii::$app->request->referrer : Url::home()?>">
<div class="row highlight">
    <div class="col-sm-1 text-right">
        <i class="image glyphicon glyphicon-folder-open"></i>
    </div>
    <div class="col-sm-5 text-left">..</div>
    <div class="col-sm-3 text-right"></div>
</div>
</a>
<?php endif; ?>
<?php foreach ($list as $item): ?>
<?php if($item['type']=='directory'): ?>
<a href="<?=Url::current(['path'=>$path.'/'.$item['name']])?>">
<?php else: ?>
<a href="#">    
<?php endif; ?>
<div class="row highlight">
    <div class="col-sm-1 text-right">
        <?php if($item['type'] == 'directory'): ?>
        <i class="image glyphicon glyphicon-folder-open"></i>
        <?php else: ?>
        <i class="image glyphicon glyphicon-file"></i>
        <?php endif; ?>
    </div>
    <div class="col-sm-5 text-left"><?=$item['name']?></div>
    <div class="col-sm-3 text-left"><?php echo $item['month'].'/'.$item['day']; ?></div>
    <div class="col-sm-3 text-left"><?=$item['type']=='file'?intval($item['size']/1024) . ' Kb':''?></div>
</div>
</a>
<?php endforeach; ?>