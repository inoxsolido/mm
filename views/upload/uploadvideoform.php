<?php
/* @var $this yii\web\View */
/* @var $model app\models\media */
/* @var $form ActiveForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \kartik\file\FileInput;
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
            'options' => ['accept' => '*', 'multiple' => false]
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
<?php 
$this->registerJsFile("@web/js/videoform.js",  ['depends' => [\yii\web\JqueryAsset::className()]]);