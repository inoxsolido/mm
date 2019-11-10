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
                'dropZoneEnabled' => false,
                'showPreview' => true,
                'showCaption' => true,
                'showRemove' => false,
                'showUpload' => false,
                'browseClass' => 'btn btn-primary ',
                'browseIcon' => '<i class="glyphicon glyphicon-file"></i> ',
                'browseLabel' => 'เลือกไฟล์',
                'initialPreview'=>[
                    
                ],
                'initialPreviewAsData' => false,
//                'layoutTemplates' => ['preview' => '<div class="file-preview {class}">{close}'
//                    . '<div class="close fileinput-remove">×</div>'
//                    . '<div class="{dropClass}">'
//                    . '<div class="file-preview-thumbnails"></div>'
//                    . '<div class="clearfix"></div>'
//                    . '<div class="file-preview-status text-center text-success"></div> <div class="kv-fileinput-error"></div></div></div>']
                //'initialCaption'=>"The Moon and the Earth",
                //'overwriteInitial'=>false,
                //'maxFileSize'=>2800

            ],
            'options' => ['accept' => '*', 'multiple' => false]
        ]) ?>
    <?=
         $form->field($model, 'thumbnail_file', ['enableAjaxValidation' => false])->widget(FileInput::classname(),[
            'pluginOptions' => [
                'dropZoneEnabled' => false,
                'showPreview' => true,
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
            'options' => ['accept' => 'image/*', 'multiple' => false, 'require' => true]
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
<?php JSRegister::begin(['position' => \yii\web\View::POS_READY]); ?>
<script>
$(function(){
    tagbox.tagbox(".tagbox");
    
    $(document).on('change', '#media-media_file', function(){
        if(this.files[0]){
           
            
            if($("#media-name").val() == '') $("#media-name").val($(this).val().split(/(\\|\/)/g).pop());
        }else{
            $(window).on("beforeunload", function(){
                return "Bye";
            });
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
        beforeSerialize: function() { 
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
            console.log("work");
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
            successPopUp("Upload Successful");
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
            
        }
    });
   
    
});    
</script>
<?php JSRegister::end(); ?>
