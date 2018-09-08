<?php
/* @var $model \app\models\Media */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\file\FileInput;
use richardfan\widget\JSRegister;

\app\assets\JqueryFormAsset::register($this);
\app\assets\JqueryTagboxAsset::register($this);
app\assets\PlyrAsset::register($this);
?>

<div class="form">
    <?php $form = ActiveForm::begin(['enableAjaxValidation' => false]); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'tags', ['template' => '<div class="form-group"><label class="control-label">{label}</label><div  class="tagbox form-control">{input}</div></div>'])->textInput(['maxlength' => true, 'class' => 'tag-input invisible-input']) ?>
    <?= $form->field($model, 'is_public')->checkbox() ?>
    <?php if($model->album_id): ?>
    <?= $form->field($model, 'album_id')->dropDownList($albumList) ?>
    <?php endif; ?>
    <!-- MEDIA PLAYER -->
    <?php if ($model->media_type_id == 1): ?>
        <div style="width:400px; display:inline-block; margin-bottom:15px">
            <video poster="<?= $model->getThumbnailHttpPath() ?>" src="<?= $model->getHttpPath() ?>" id="player" controls data-plyr-config='{ "title": "<?= $model->name ?>"}'></video>
        </div>
    <?php elseif ($model->media_type_id == 2): ?>
    <div style="width:400px; display:inline-block; margin-bottom:15px">
        <img style="width:100%" class="image-viewer" src="<?=$model->getHttpPath()?>"/>
    </div>
    <?php elseif ($model->media_type_id == 3): ?>
    <div style="width:400px; display:inline-block; margin-bottom:15px">
        <audio  src="<?= $model->getHttpPath() ?>" id="player" controls data-plyr-config='{ "title": "<?= $model->name ?>"}'></audio>
    </div>
    <?php else: ?>
    <div style="width:400px; display:inline-block; margin-bottom:15px">
        <iframe src="https://docs.google.com/viewer?url=<?=$model->getHttpPath()?>&embedded=true" style="width:400px; height:auto;" frameborder="0"></iframe>
    </div>
    <?php endif; ?>
    <!-- THUMBNAIL -->
    <?php if ($model->media_type_id != 2): ?>
        <?=
        $form->field($model, 'thumbnail_file', ['enableAjaxValidation' => false])->widget(FileInput::classname(), [
            'pluginOptions' => [
                'showPreview' => true,
                'showCaption' => true,
                'showRemove' => false,
                'showUpload' => false,
                'browseClass' => 'btn btn-primary ',
                'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                'browseLabel' => 'เลือกภาพตัวอย่างใหม่',
                'initialPreview' => [
                    "<img src='" . $model->getThumbnailHttpPath() . "' style='width:100%' class='file-preview-image' >",
                ],
                'initialPreviewAsData' => false,
                'fileActionSettings'=>[
                    'showRemove'=>false,
                    'showDrag'=>false,
                ],
            //'initialCaption'=>"The Moon and the Earth",
            //'overwriteInitial'=>false,
            //'maxFileSize'=>2800
            ],
            'options' => ['accept' => 'image/*', 'multiple' => false]
        ])
        ?>
    <?php endif; ?>
    <div class="form-group">
        <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
    <?php JSRegister::begin() ?>
<script>
    tagbox.tagbox(".tagbox");
    const player = new Plyr('#player');
    //start form
    var bar = $('.bar');
    var percent = $('.percent');
    // var status = $('#status');
    $('#w0').submit(function(e){
//        e.preventDefault();
////        if($("#media-name").val() === '' || $("#media-media_file").val() == '' || ($("#media-name"))){
////            return false;
////        }
        $("#loading").show();
        $('#w0').ajaxForm({
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
//                window.location = responseText;
                console.log(responseText);
            },
            error: function(xhr){
                bar.width(0);
                percent.html(xhr.reponseText);
    //            errorPopUp(xhr.reponseText);
                $("#loading").hide();
            },
            complete: function (xhr) {
                // status.html(xhr.responseText);
                console.log(xhr.responseText);

                $(".tag-input").val('').show();
                //restore tag value to tag chip
                $(".tag").each(function(){
                    $(this).tagvalue($(this).val());
                    $(this).prop({disabled:false});
                });
                $("#loading").hide();
            }
    });
    });
    
    //--end form
</script>
<?php JSRegister::end(); ?>