<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use richardfan\widget\JSRegister;

\app\assets\PlyrAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\MediaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Media';
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="player-backdrop" class="player-back-drop" style="display:none;"></div>
<div id="player" class="player" style="display:none;">
    <div class="player-header">
        <div class="player-icon">
            <i style="line-height:50px;font-size:20px;" class="glyphicon glyphicon-folder-open"></i>
        </div>
        <div class="player-title">

            <a href="#" class="player-album">album_name</a>
            <span>&nbsp;-&nbsp;</span>
            <span class="player-name">media_name</span>

        </div>
        <div class="player-close">
            <i style="line-height:50px;font-size:20px" class="fa fa-close"></i>
        </div>
    </div>
    <div class="player-content">
        <div class="player-left"><a href="#" style="display:table-cell; vertical-align: middle;"><i class="glyphicon glyphicon-backward"></i></a></div>
        <div class="player-media"><div class="player-media-wrapper"></div></div>
        <div class="player-right"><a href="#" style="display:table-cell; vertical-align: middle;"><i class="glyphicon glyphicon-forward"></i></a></div>
    </div>
    <div class="player-footer">
        <div class="player-tag-container">
            <div class="">
                <div class="player-tag-icon">
                    <i style="color:white;font-size:20px;" class="glyphicon glyphicon-tags"></i>
                    <span>&nbsp;&nbsp;</span>
                </div>
                <span class="player-tag">
                <a href="#">tag</a>
                <a href="#">tag</a>
                <a href="#">tag</a>
                <a href="#">tag</a>
                <a href="#">tag</a>
                <a href="#">tag</a>
                </span>
            </div>
        </div>
        <div class="player-download">
            <a href="#" class="btn btn-lg btn-default">Download</a>
        </div>
    </div>
</div>
<div class="media-index">

    <h1><?= Html::encode($this->title) ?></h1>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'header'=>'<input id="check-main-all" type="checkbox" class="check-main-all"/>',
                'headerOptions' => ['style'=>['text-align'=>'center']],
                'value' => function($model){return '<input type="checkbox" class="check-main" value="'.$model->id.'"/>';},
                'format' => 'raw'
            ],
            [
                'label' => 'ภาพตัวอย่าง',
                'headerOptions' => ['style' => 'min-width:200px'],
                'value' => function($model){
                    
                    return Html::a(Html::img($model->getThumbnailHttpPath(), ['style'=>['width'=>'200px']]),"#{$model->id}",[
                        'title' => Yii::t('yii', 'View'),
                        'data' => ['id'=>$model->id, 'name'=>$model->name, 'tags'=>$model->tags, 'poster'=>$model->getThumbnailHttpPath(),
                            'flink'=>$model->getHttpPath(), 'type'=>$model->media_type_id, 
                            'album_name'=>$model['album']['name'], 'album_link'=>Url::to(['/album/view','id'=>$model->album_id])],
                        'class'=>'previewable image'
                    ]);
                },
                'format' => 'raw'
            ],

            'name',
            [
                'attribute' => 'album_id',
                'value' => function($model){
                    if(@$model['album']['name'] != null)
                        return Html::a($model['album']['name'], Url::to(['album/index','AlbumSearch[name]'=>$model['album']['name']]));
                    return null;
                },
                'format'=>'html',
            ],
            [
                'attribute' => 'tags',
                'value'=>function($model){
                    if(@$model->tags == null)return null;
                    $tags = explode(',', $model->tags);
                    $html = '';
                    foreach($tags as $tag){
                        $html .= Html::a($tag, Url::current(['MediaSearch[tags]'=>$tag])).'&nbsp;&nbsp;';
                    }
                    return $html;
                },
                'format' => 'html',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'media_type_id',
                'filter'=> yii\helpers\ArrayHelper::map(\app\models\MediaType::find()->all(),'id','name'),
                'value' => 'mediaType.name'
            ],
            [
                'attribute' => 'file_upload_date',
                'value' => function($model){ return Yii::$app->utility->strDateReformat($model['file_upload_date'], 'd/m/Y H:i');},
                'format' => 'html',
                'filter' => kartik\daterange\DateRangePicker::widget([
                        'name'=>'dr',
                        'hideInput'=>true,
                        'convertFormat'=>true,
                        'pluginOptions'=>[
     
                            'timePicker'=>true,
                            "timePicker24Hour"=> true,
                            'timePickerIncrement'=>1,
                            'locale'=>['format'=>'Y-m-d H:i:s'],
                        ],
                        'pluginEvents'=>[
                            'cancel.daterangepicker' => 'function() { $("[name=dr]").val(""); $("[name=dr]").parent().find(".range-value").text(""); }',
                        ]
                    ]),
            ],
            [
                'label' => 'สิทธิ์เข้าถึง',
                'attribute' => 'is_public',
                'value' => function($data){return @$data['is_public']==0?'Private': 'Public';},
                'filter' => [0=>'Private',1=>'Public'],
                'format' => 'text',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '<button id="media-delete" class="btn btn-danger">Delete Selected Media</button>',
                'template' => "{view} {update} {delete}",
                'buttons' => [
                    'view' => function ($url, $model, $index) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', "#{$model->id}", [
                            'title' => Yii::t('yii', 'View'),
                            'data' => ['id'=>$model->id, 'name'=>$model->name, 'tags'=>$model->tags, 'poster'=>$model->getThumbnailHttpPath(),
                                'flink'=>$model->getHttpPath(), 'type'=>$model->media_type_id, 
                                'album_name'=>$model['album']['name'], 'album_link'=>Url::to(['/album/view','id'=>$model->album_id])],
                            'class'=>'previewable view'
                        ]);
                    },
                    'update' => function ($url, $model, $index) {
                        $url = Url::to(["media/media-edit",'id'=>$index]);
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'title' => Yii::t('yii', 'Update'),
                        ]);
                    },


                ],

            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
<?php JSRegister::begin(['position'=> \yii\web\View::POS_READY]); ?>
<script>
    //lib
    var controls = {controls: [
        'play-large', // The large play button in the center
//        'restart', // Restart playback
//        'rewind', // Rewind by the seek time (default 10 seconds)
        'play', // Play/pause playback
//        'fast-forward', // Fast forward by the seek time (default 10 seconds)
        'progress', // The progress bar and scrubber for playback and buffering
        'current-time', // The current time of playback
        'duration', // The full duration of the media
        'mute', // Toggle mute
        'volume', // Volume control
//        'captions', // Toggle captions
//        'settings', // Settings menu
//        'pip', // Picture-in-picture (currently Safari only)
//        'airplay', // Airplay (currently Safari only)
//        'download', // Show a download button with a link to either the current source or a custom URL you specify in your options
        'fullscreen', // Toggle fullscreen
    ]};
    var controls = {controls: { muted: true, volume: true } };
    var media_set = [];  
    $("body").on("click", ".previewable", function(){
        /* step 
         * 1 check type
         * 2 collect .previewable infomation
         * 3 find this position
         * 4 create mediaElement
         * 5 put next and prevous to button
         * 6 put mediaElement to template
         * 7 show up !
         */
        let this_class = $(this).attr('class').split(" ");
        this_class = this_class.map(function(v){return "."+v;});
        let selector = this_class.join("");
        let full_link = $(this).data('flink');
        let poster = $(this).data('poster');
        let type = $(this).data('type');
        let id = $(this).data('id');
        let tags = $(this).data('tags');
        let album_name = $(this).data('album_name');
        let album_link = $(this).data('album_link');
        let name = $(this).data('name');
        let $previewable = $(selector);
        var media_set = [];//reset
        //get position
        let prev,next,pos = 0;
        $previewable.each(function(){
            media_set.push($(this).data('id'));
        });
        pos = media_set.indexOf(id);
        prev = pos - 1,next = pos + 1;
        let mediaElement = '';
        switch(type){
            case 1:
                mediaElement = $('<video id="media-player" controls volume src="'+full_link+'" poster="'+poster+'">Your browser does not support the video tag.</video>');
                break;
            case 2:
                mediaElement = $('<div/>').append($('<img/>',{src: full_link}));
                break;
            case 3:
                mediaElement = $('<audio id="media-player" controls src="'+full_link+'" poster="'+poster+'"></audio>');
                break;
            case 4:
                mediaElement = $('<span><i class="fa fa-file-o" style="font-size:200px"></i></span>');
                break;
            case 5:
                mediaElement = $('<span><i class="fa fa-file-o" style="font-size:200px"></i></span>');
                break;
        }
        if(mediaElement === '') return false;
        
        //name
        $(".player-name").text(name);
        //album
        $player_album = $(".player-album");
        if(album_name != ''){
            $player_album.attr({href:album_link}).text(album_name).show();
            $player_album.next("span").show();
        }
        else{
            $player_album.next("span").hide();
            $player_album.attr({href:"#"}).text("").hide();
            
        }
        //tag
        $(".player-tag > *").remove();
        let splited_tag = String(tags).split(",");
        $.each(splited_tag, function(){
            $(".player-tag").append($("<a/>",{href:"<?= Url::to(['search/index','omediatag'=>1,'oalbumtag'=>1]) ?>&q="+this}).text(this));
        });
        
        //put prev, next btn
        if(prev < 0){ // disable prev btn
            $(".player-left > a").eq(0).attr({href: "javascript:;"});
            $(".player-left").hide();
        }else{
            $(".player-left > a").eq(0).attr({href: "javascript:;"}).data({id:media_set[prev]});
            $(".player-left").show();
        }
        
 
        if(next >= media_set.length){//disable next btn
            $(".player-right > a").eq(0).attr({href: "javascript:;"});
            $(".player-right").hide();
        }else{
            $(".player-right > a").eq(0).attr({href: "javascript:;"}).data({id:media_set[next]});
            $(".player-right").show();
        }
        $("#player").find(".player-media-wrapper").html(mediaElement);
        $(".player-download > a").eq(0).attr({href:full_link, target:"_blank"});
        $("#player-backdrop").show();
        $("#player").show();
        Plyr.setup("#media-player", controls);
        $(".plyr__volume").removeAttr("hidden");
        $(".plyr__control").removeAttr("hidden");
        window.location.hash = id;
    });
    $("#player").on("click", ".player-left > a, .player-right > a", function(){
        if($(this).is(":visible") === false) return false;
        let id = $(this).data('id');
        if(id){
            let $target = $(".previewable[data-id="+id+"]:first");
            if($target.length) $target.click();
        }
    });
    $("#player").on("click", ".player-close", function(){
        $("#player").find(".player-media-wrapper").html('');
        $("#player-backdrop").hide();
        $("#player").hide();
        window.location.hash = "";
    });
    $(document).keyup(function(e){
        if(e.key === "Escape"){
            $(".player-close").click();
        }else if(e.key === "ArrowLeft"){
            $(".player-left > a").click();
        }else if(e.key === "ArrowRight"){
            $(".player-right > a").click();
        }
    });
    function captureUrl(){
        let hash = window.location.hash;
        let id = hash.replace("#","");
        if(id == "")return false;
        let $target = $(".previewable[data-id="+id+"]:first");
        if($target.length) $target.click();
    }
    
    $("#media-delete").click(function(){
        var media_id_set,
        media_checked = $(".check-main:checked");
        if(!media_checked.length){
            errorPopUp("กรุณาเลือกไฟล์ก่อน");
            return false;
        }else{
            if(!confirm('Are you sure you want to delete these item?')) return false;
            $("#loading").show();
            media_id_set = $.map(media_checked, function(media){
                return $(media).val();
            });
            $.ajax({
                url: "<?= Url::to(['media/delete-selected-media'])?>",
                type: "POST",
                async: false,
                data:{
                    media_id_set: media_id_set
                }
            }).done(function(data, textStatus, jqXHR){
//                successPopUp("ลบข้อมูลสำเร็จ");
                $(".check-main:checked").parents('tr').remove();
                window.location.reload();
            }).fail(function(jqXHR,textStatus,errorThrown){
                if(jqXHR.status === 500){
                    errorPopUp(jqXHR.responseText);
                }else{
                    errorPopUp(textStatus);
                }
            });
        }
        
    });
    $("#p0").on("change",".check-main", function(){
        var checked = $(".check-main:checked").length;
        var checkbox_all = $(".check-main").length;
        if(checked === checkbox_all){
            $(".check-main-all").prop({checked: true});
            $(".check-main-all").prop({indeterminate: false});
        }else if(checked === 0){
            $(".check-main-all").prop({checked: false});
            $(".check-main-all").prop({indeterminate: false});
        }else{
            $(".check-main-all").prop({indeterminate: true});
            $(".check-main-all").prop({checked: false});
        }
        
    });
    
    $("#p0").on("change", ".check-main-all", function(){
        var state = $(".check-main-all").is(":checked");
        $(".check-main").prop({checked: state});
    });
    
    captureUrl();
</script>
<?php JSRegister::end(); ?>
