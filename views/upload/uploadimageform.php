<?php
/* @var $this yii\web\View */
/* @var $model app\models\Album */
/* @var $form ActiveForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\file\FileInput;
use yii\helpers\Url;
use richardfan\widget\JSRegister;
//use \app\components\FileUploadUICustom;
//use dosamigos\fileupload\FileUploadUI;
//use dosamigos\fileinput\FileInput;
\app\assets\JqueryFormAsset::register($this);
\app\assets\JqueryTagboxAsset::register($this);
?>
<style>
    .selection-wrapper{
        display:inline-block;
        margin: 5px;
        padding: 0px;
        text-align: center;
        min-width: 200px;
        min-height: 200px;

    }
    .selection-box{
        display:inline-block;
        margin: 0px;
        padding: 10px;
        width: 45%;
        height: 45%;
        vertical-align: middle;
        text-align: center;
    }
    .selection-box > img{
        width:100px;
        margin:auto;
    }
    .bar { background-color: #0aff4c; width:0%; height:20px;border:none }
    .percent { position:absolute; display:inline-block; top:0px; left:48%; color:white; font-weight: bold; border-color: #51ff80; border-radius: 3px; }
    .progress {border:none;};
</style>
<?php if($getAlbum != "new" && $getAlbum != "exist"): ?>
<div id="choice">
    <h2>Create new album ?</h2>
    <center>
        <div class="selection-wrapper" >
            <a href="<?=Url::current(['type'=>'image', 'album'=>'new'])?>" class="selection-box btn btn-default">
Yes            </a>
            <a href="<?=Url::current(['type'=>'image', 'album'=>'exist'])?>" class="selection-box btn btn-default">
Use exist album
            </a>
    </center>
</div>
<?php else: ?>


<div id="album" >
    <div class="form">
        <?php $form = ActiveForm::begin(['enableAjaxValidation'=>true,]);?>
        <?php if($getAlbum == "new"): ?>
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'tags', ['template' => '<div class="form-group"><label class="control-label">{label}</label><div  class="tagbox form-control">{input}</div></div>'])->textInput(['maxlength' => true, 'class'=>'tag-input invisible-input']) ?>
        <?php elseif($getAlbum == "exist"): ?>
        <?= $form->field($model, 'name' ,['enableAjaxValidation'=>false])->dropDownList($list_album) ?>
        <?php endif; ?>
        <input type="hidden" name="type" value="2"/>
        <?=
         $form->field($model, 'files[]', ['enableAjaxValidation' => false])->widget(FileInput::classname(),[
            'pluginOptions' => [
                'showCaption' => true,
                'showRemove' => false,
                'showUpload' => false,
                'browseClass' => 'btn btn-primary ',
                'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                'browseLabel' => 'เลือกไฟล์',
                'initialPreview'=>[

                ],
                'initialPreviewAsData' => true,
                //'initialCaption'=>"The Moon and the Earth",
                'overwriteInitial'=>false,
                //'maxFileSize'=>2800

            ],
            'options' => ['accept' => app\models\MediaType::getExtensionAsString(['image'],true,true), 'multiple' => true]
        ]) ?>
        <div class="form-group">
            <?= Html::submitButton('Upload', ['id'=>'btnsubmit', 'class' => 'btn btn-primary', 'disabled'=>'disabled']) ?>
        </div>
        <?php ActiveForm::end();?>

    </div>
    
    <div class="progress" style="width:100%; display:none;">
        <div class="bar"></div >
        <div class="percent">0%</div >
    </div>

<!--    <pre id="status"></pre>-->
</div>
<?php JSRegister::begin(['position'=> yii\web\View::POS_READY]); ?>
<script>
$(function(){
    tagbox.tagbox(".tagbox");
    
    $("body").on('click', '.close', function(){
        $("#album-files").val('').change();
    });
    $("#album-files").change(function(){
        $("#btnsubmit").prop({disabled:true});
        //clear old thumbnail data
        $("input[name=thumbnails\\[\\]]").remove();
        //add new data;
        addThumbnailToForm();
    });
    function addThumbnailToForm(){
        var thumbnail_data = [];
        let $files = $("#album-files");
        let files = $files[0].files;
        var i=0;
        if($files[0].files.length){
            
            $.each(files, function(key, value){
                let reader = new FileReader();
                reader.onload = function(readerEvent){
                    let image = new Image();
                    image.onload = function(imageEvent){
                        
                        let canvas = document.createElement('canvas'),
                            width_lim = 300,//px
                            ratio = 9/16,//for height*3
                            height_lim = ratio * width_lim,
                            width = image.width,
                            height = image.height,
                            x=0,y=0;

                        if(width > width_lim){
                            width = width_lim;
                            height = height * (width_lim/image.width) ;
                        }
                           
                        if(height > height_lim){//still more than limit
                            //crop only y
                             y = height - height_lim;//eg.112.5 - 200
                        }
                            
                        
                        canvas.width = width;
                        canvas.height = height - y;
                        
                        let context = canvas.getContext('2d');
                        context.drawImage(image, 0, -(y/2), width, height);
                        
                        var dataUrl = canvas.toDataURL('image/jpeg', 1);
                        thumbnail_data.push(dataUrl);
                        $('<input />').attr('type', 'hidden')
                            .attr('name', "thumbnails[]")
                            .attr('value', dataUrl).appendTo('form');
                        
                    };
                    image.src = readerEvent.target.result;
                    
                };
                reader.onloadend = function(readerEvent){
                    console.log(i);
                    if(i===files.length){
                        $("#btnsubmit").prop({disabled:false});
                    }else{
                        $("#btnsubmit").prop({disabled:true});
                    }
                    
                };
                reader.readAsDataURL(value);
                i++;
            });
        }
        console.log(i);
        return thumbnail_data;
           
    }
    //start form
    var bar = $('.bar');
    var percent = $('.percent');
    // var status = $('#status');
//    $('form').submit(function(e){
////        let $files = $("#album-files");
//        if($files.val())
//            $("#loading").show();
//        console.log('summited');
//        
//        $('')
//        
//        return false;
//    });
    $('form').on('beforeSubmit', function (e) {
        $("#loading").show();
        return true;
    });
    $('form').ajaxForm({
        data:{
            type:"2",
            submit: '1'
            },
//        forceSync: true,
//        async:false,
        beforeSerialize: function($form, opt) { 
            
            //tag
            $(".tag-input").each(function(){
               $(this).val($(this).tagvalue());
               $(this).hide();
            });
            // return false to cancel submit                  
        },
        beforeSend: function (xhr, settings) {
        // status.empty();
            var percentVal = '0%';
            bar.width(percentVal);
            percent.html(percentVal);
            $(percent).parent().show();
        },
        beforeSubmit: function(arr, $form, options) { 
            // The array of form data takes the following form: 
            // [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ] 

            // return false to cancel submit                  
        },
        uploadProgress: function (event, position, total, percentComplete) {
            var percentVal = percentComplete + '%';
            bar.width(percentVal);
            percent.html(percentVal);
            //console.log(percentVal, position, total);
        },
        success: function (responseText) {
            var percentVal = '100%';
            bar.width(percentVal);
            percent.html(percentVal);
            successPopUp("Upload Successful");
            window.location = responseText;
        },
        error: function (xhr) {
            bar.width(0);
            $("#album-files").change();
            errorPopUp(xhr.responseText);
            percent.html(xhr.responseText);
            $("#loading").hide();
        },
        complete: function (xhr) {
            // status.html(xhr.responseText);
            $(".tag-input").val('').show();
            //restore tag value to tag chip
            $(".tag").each(function () {
                $(this).tagvalue($(this).val());
                $(this).prop({disabled: false});
            });
            $("#loading").hide();
        }
    });
    
});
</script>
<?php JSRegister::end(); ?>

<?php endif; ?>

