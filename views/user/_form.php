<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'readonly' => !$model->isNewRecord]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'value'=>'']) ?>
    
    <?= $form->field($model, 'password_confirm')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    
    <?php $imageUrl = '/'.$model->image_path;
    echo $form->field($model, 'image_file')->widget(FileInput::classname(),[
        'pluginOptions' => [
            'showCaption' => false,
            'showRemove' => false,
            'showUpload' => false,
            'browseClass' => 'btn btn-primary ',
            'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
            'browseLabel' => 'เลือกรูปภาพ',
            'initialPreview'=> $model->image_path?[$imageUrl]:'',
            'initialPreviewAsData'=> true,
            //'initialPreviewFileType'=> 'image',
//            'initialPreviewConfig'=> [
//            ['caption'=> "Desert.jpg", 'size'=> 827000, 'width'=> "120px", 'url'=> "/file-upload-batch/2", 'key'=> 1],
//            ['caption'=> "Lighthouse.jpg", 'size'=> 549000, 'width'=> "120px", 'url'=> "/file-upload-batch/2", 'key'=> 2],
//            ['type'=> "video", 'size'=> 375000, 'filetype'=> "video/mp4", 'caption'=> "KrajeeSample.mp4", 'url'=> "/file-upload-batch/2", 'key'=> 3],
//            ['type'=> "pdf", 'size'=> 8000, 'caption'=> "About.pdf", 'url'=> "/file-upload-batch/2", 'key'=> 4],
//            ['type'=> "text", 'size'=> 1430, 'caption'=> "LoremIpsum.txt", 'url'=> "/file-upload-batch/2", 'key'=> 5],
//            ['type'=> "html", 'size'=> 3550, 'caption'=> "LoremIpsum.html", 'url'=> "/file-upload-batch/2", 'key'=> 6]
//            ],
        ],
        'options' => ['accept' => 'image/*']
    ]);
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_type_id')->dropDownList(yii\helpers\ArrayHelper::map(app\models\UserType::find()->all(), 'id', 'name')) ?>
    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
