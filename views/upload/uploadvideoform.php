<?php
/* @var $this yii\web\View */
/* @var $model app\models\media */
/* @var $form ActiveForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\file\FileInput;
use richardfan\widget\JSRegister;
use yii\helpers\Url;
//use \app\components\FileUploadUICustom;
//use dosamigos\fileupload\FileUploadUI;
//use dosamigos\fileinput\FileInput;
\app\assets\JqueryFormAsset::register($this);
\app\assets\JqueryTagboxAsset::register($this);
?>

<div class="form">
    <?php $form = ActiveForm::begin(['enableAjaxValidation' => false]);?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'tags', ['template' => '<div class="form-group"><label class="control-label">{label}</label><div  class="tagbox form-control">{input}</div></div>'])->textInput(['maxlength' => true, 'class'=>'tag-input invisible-input']) ?>
    <?= $form->field($model, 'is_public')->checkbox() ?>
    <?=
         $form->field($model, 'media_file', ['enableAjaxValidation' => false])->widget(FileInput::classname(),[
            'pluginOptions' => [
                'showPreview' => false,
                'showCaption' => true,
                'showRemove' => false,
                'showUpload' => false,
                'browseClass' => 'btn btn-primary ',
                'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                'browseLabel' => 'เลือกไฟล์',
                'initialPreview'=>[

                ],
                'initialPreviewAsData' => false,
                //'initialCaption'=>"The Moon and the Earth",
                //'overwriteInitial'=>false,
                //'maxFileSize'=>2800

            ],
            'options' => ['accept'=> app\models\MediaType::getExtensionAsString(['video'], true, true) ,'multiple' => false]
        ]) ?>
    <div class="file-preview" id="video-preview-box" style="display:none;">
        <div id="close-video-preview" class="close fileinput-remove">×</div>
        <div class="">
            <div class="file-preview-thumbnails" style="display:inline-block">
                <div class="file-preview-frame" id="preview-1528118551310-0" data-fileindex="0" title="" style="width:213px;height:160px;">
                    <video id="video-el" width="500" controls="">
                        <source id="video-src" src="">
                        <div class="file-preview-other">
                            <i class="glyphicon glyphicon-file"></i>
                        </div>
                    </video>
                    <div class="file-thumbnail-footer">
                        <div class="file-caption-name"></div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>    <div class="file-preview-status text-center text-success"></div>
            <div class="kv-fileinput-error file-error-message" style="display: none;"></div>
        </div>
    </div>
    <div class="file-preview" id="thumbnail-preview-box" style="display:none">
        <div id="close-thumbnail-preview" class="close fileinput-remove">×</div>
        <div class="">
            <div class="file-preview-thumbnails">
                <div class="file-preview-frame" id="preview-1528122079811-0" data-fileindex="0">
                    <img id="img-el" src="#" class="file-preview-image" title="" alt="" style="width:auto;height:160px;">
                    <div class="file-thumbnail-footer">
                        <div class="file-caption-name" title="" style="width: 295px;"></div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>    <div class="file-preview-status text-center text-success"></div>
            <div class="kv-fileinput-error file-error-message" style="display: none;"></div>
        </div>
    </div>
    
    <?=
         $form->field($model, 'thumbnail_file', ['enableAjaxValidation' => false])->widget(FileInput::classname(),[
            'pluginOptions' => [
                'showPreview' => false,
                'showCaption' => true,
                'showRemove' => false,
                'showUpload' => false,
                'browseClass' => 'btn btn-primary ',
                'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                'browseLabel' => 'เลือกภาพตัวอย่าง',
                'initialPreview'=>[

                ],
                'initialPreviewAsData' => false,
                //'initialCaption'=>"The Moon and the Earth",
                //'overwriteInitial'=>false,
                //'maxFileSize'=>2800

            ],
            'options' => ['accept' => 'image/*', 'multiple' => false]
        ]) ?>
    <button id="btnusethumbnail" type="button" title="Use thumbnail from video" class="btn btn-default" style="display:none">
        <i class="glyphicon glyphicon-ban-circle"></i> ใช้ภาพตัวอย่างจากวิดีโอ
    </button>
    <input id="thumbnail_from_video" type="hidden" name="Media[thumbnail_from_video]"/>
    <div class="thumbnail-preview" style="display:none;"><canvas id="thumbnail-canvas"></canvas></div>
        
    <div class="form-group">
        <?= Html::submitButton('Upload', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>




<div class="progress" style="width:100%; display:none;">
      <div class="bar"></div >
      <div class="percent">0%</div >
</div>
<?php JSRegister::begin(['position'=> \yii\web\View::POS_READY]); ?>
<script>
    $(function(){
    tagbox.tagbox(".tagbox");
    
    $(document).on('change', '#media-media_file', function(){
        if(this.files[0]){
            var $source = $("#video-src");
            $source[0].src = URL.createObjectURL(this.files[0]);
            $source.parent()[0].load();
            $(this).parents('.input-group').before($("#video-preview-box"));
            $("#video-preview-box").show();
            $("#media-thumbnail_file").parent().before($("#btnusethumbnail"));
            $("#btnusethumbnail").show();
            
            if($("#media-name").val() == '') $("#media-name").val($(this).val().split(/(\\|\/)/g).pop());
        }else{
            $(this).parents('.input-group').find('.file-caption-name').text("");
            $("#btnusethumbnail").hide();
        }
    });
    $("#close-video-preview").click(function(){
        $("#video-preview-box").hide();
        $("#media-media_file").val("").change();
        $("#video-el")[0].pause();
        $("#video-src")[0].src = "";
        $("#video-el")[0].load();
    });
    $("#close-thumbnail-preview").click(function(){
        $("#thumbnail-preview-box").hide();
        $("#media-thumbnail_file").val("");//clear file input
        $("#img-el").parent().find(".file-caption-name").text("");
        $("#thumbnail_from_video").val("");
    });
    // Get handles on the video and canvas elements
    var video = document.querySelector('video');
    var canvas = document.querySelector('canvas');
    // Get a handle on the 2d context of the canvas element
    var context = canvas.getContext('2d');
    // Define some vars required later
    var w, h, ratio;

    // Add a listener to wait for the 'loadedmetadata' state so the video's dimensions can be read
    video.addEventListener('loadedmetadata', function() {
            // Calculate the ratio of the video's width to height
            ratio = video.videoWidth / video.videoHeight;
            // Define the required width as 100 pixels smaller than the actual video's width
            w = video.videoWidth - 100;
            // Calculate the height based on the video's width and the ratio
            h = parseInt(w / ratio, 10);
            // Set the canvas width and height to the values just calculated
            canvas.width = w;
            canvas.height = h;			
    }, false);

    // Takes a snapshot of the video
    function snap() {
            if(!$("#media-media_file").val()){ errorPopUp('Please choose file before.'); return false;}
            // Define the size of the rectangle that will be filled (basically the entire element)
            context.fillRect(0, 0, w, h);
            // Grab the image from the video
            context.drawImage(video, 0, 0, w, h);
            
            var canvas = $("#thumbnail-canvas")[0];
            $("#media-thumbnail_file").val("");//clear file input
            $("#img-el").parent().find(".file-caption-name").text("");
            $("#thumbnail_from_video").val(canvas.toDataURL('image/jpeg',0.75)); //put file to hidden input
            $("#img-el")[0].src = $("#thumbnail_from_video").val();
            $("#thumbnail-preview-box").show();
    }

    $("#btnusethumbnail").click(snap);
    //---
    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#img-el")[0].src = e.target.result;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    $(document).on("change", "#media-thumbnail_file", function(){
        if(this.files[0]){
            $("#thumbnail_from_video").val("");
            readURL(this);
            $("#thumbnail-preview-box").show();
        }else{
            
        }
    });
    //--
    //start form
    var bar = $('.bar');
    var percent = $('.percent');
    // var status = $('#status');
    
    $('form').on('beforeSubmit', function (e) {
        $("#loading").show();
        return true;
    });
    $('form').ajaxForm({
        data:{
            type:"1",
            submit: '1'
        },
        forceSync: true,
        beforeSerialize: function($form, options) { 
            $(".tag-input").each(function(){
               $(this).val($(this).tagvalue());
               $(this).hide();
            });

            // return false to cancel submit                  
        },
        beforeSend: function () {
            // status.empty();
            var percentVal = '0%';
            bar.width(percentVal);
            percent.html(percentVal);
            $(percent).parent().show();
            //set tag value
            //set tags label into textinput
            // var tags = [];
            // $(".tag").each(function(){
            //     tags.push($(this).text());
            // });
            // $(".tagname").val(tags.join(";")+";");


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
            successPopUp('Upload success');
            window.location = responseText;
        },
        error: function(xhr){
            bar.width(0);
            percent.html(xhr.responseText);
            errorPopUp(xhr.responseText);
            $("#loading").hide();
        },
        complete: function (xhr) {
            // status.html(xhr.responseText);
            $(".tag-input").val('').show();
            //restore tag value to tag chip
            $(".tag").each(function(){
                $(this).tagvalue($(this).val());
                $(this).prop({disabled:false});
            });
            $("#loading").hide();
        }
    });
    //--end form
    
    
    
});



</script>
<?php JSRegister::end(); ?>